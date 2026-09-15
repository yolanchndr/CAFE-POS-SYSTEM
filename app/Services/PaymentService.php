<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Services\QueueNumberService;
use Exception;

class PaymentService
{
    protected $db;
    protected $orderModel;
    protected $paymentModel;
    protected $queueNumberService;

    public function __construct()
    {
        $this->db                 = \Config\Database::connect();
        $this->orderModel         = new OrderModel();
        $this->paymentModel       = new PaymentModel();
        $this->queueNumberService = new QueueNumberService();
    }

    /**
     * Memproses Pembayaran Transaksi (Cash/Non-Cash) & Generate Queue Number
     */
    public function processPayment(array $payload): array
    {
        $orderId       = (int) ($payload['order_id'] ?? 0);
        $paymentMethod = $payload['payment_method'] ?? 'cash';
        $provider      = $payload['payment_provider'] ?? null;
        $amountPaid    = (float) ($payload['amount_paid'] ?? 0);
        $refNumber     = $payload['reference_number'] ?? null;

        $order = $this->orderModel->find($orderId);
        if (!$order) {
            throw new Exception('Pesanan tidak ditemukan.');
        }

        $existingPayment = $this->paymentModel->where('order_id', $orderId)->first();
        if ($existingPayment) {
            throw new Exception('Pesanan ini sudah dibayar sebelumnya.');
        }

        $amountDue = (float) $order['grand_total'];

        if ($paymentMethod === 'cash') {
            if ($amountPaid < $amountDue) {
                throw new Exception('Uang pembayaran kurang dari total tagihan.');
            }
            $changeAmount = $amountPaid - $amountDue;
        } else {
            $amountPaid   = $amountDue;
            $changeAmount = 0.00;
        }

        $this->db->transBegin();

        try {
            // 1. Simpan Rekam Pembayaran
            $paymentData = [
                'order_id'         => $orderId,
                'payment_method'   => $paymentMethod,
                'payment_provider' => $provider,
                'amount_due'       => $amountDue,
                'amount_paid'      => $amountPaid,
                'change_amount'    => $changeAmount,
                'reference_number' => $refNumber,
                'notes'            => $payload['notes'] ?? null,
                'paid_at'          => date('Y-m-d H:i:s'),
            ];

            $paymentId = $this->paymentModel->insert($paymentData);

            // 2. Transisi Status Order ke DIPROSES
            $this->orderModel->update($orderId, ['status' => 'diproses']);

            // 3. Generate Nomor Antrian Harian (Atomic Safe)
            $queue = $this->queueNumberService->generateQueueNumber($orderId);

            $this->db->transCommit();

            return [
                'status'           => true,
                'payment_id'       => $paymentId,
                'amount_due'       => $amountDue,
                'amount_paid'      => $amountPaid,
                'change_amount'    => $changeAmount,
                'queue_number'     => $queue['formatted_number'],
                'order_number'     => $order['order_number'],
            ];
        } catch (Exception $e) {
            $this->db->transRollback();
            throw new Exception($e->getMessage());
        }
    }
}