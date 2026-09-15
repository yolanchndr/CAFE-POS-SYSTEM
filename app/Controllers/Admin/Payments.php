<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentModel;

class Payments extends BaseController
{
    protected $paymentModel;

    public function __construct()
    {
        helper(['form', 'log']);
        $this->paymentModel = new PaymentModel();
    }

    public function index()
    {
        // Ambil riwayat pembayaran beserta detail order & antrian
        $payments = $this->paymentModel->select('payments.*, orders.order_number, orders.order_type, orders.grand_total, queue_numbers.formatted_number as queue_number')
                                       ->join('orders', 'orders.id = payments.order_id', 'left')
                                       ->join('queue_numbers', 'queue_numbers.order_id = orders.id', 'left')
                                       ->orderBy('payments.id', 'DESC')
                                       ->findAll();

        // Catat ke Log Aktivitas
        log_activity('VIEW_PAYMENTS', 'Membuka halaman riwayat pembayaran transaksi.');

        $data = [
            'title'    => 'Riwayat Pembayaran',
            'payments' => $payments,
        ];

        return view('admin/payments/index', $data);
    }
}