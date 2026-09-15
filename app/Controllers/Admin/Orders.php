<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\OrderItemToppingModel;
use App\Models\PaymentModel;
use App\Models\QueueNumberModel;
use App\Models\TableModel;
use App\Services\RealtimeNotifierService;
use App\Services\ReceiptPrinterService;

class Orders extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $orderItemToppingModel;
    protected $paymentModel;
    protected $queueNumberModel;
    protected $tableModel;
    protected $notifier;
    protected $receiptPrinterService;

    public function __construct()
    {
        helper(['form', 'log']);
        $this->orderModel            = new OrderModel();
        $this->orderItemModel        = new OrderItemModel();
        $this->orderItemToppingModel = new OrderItemToppingModel();
        $this->paymentModel          = new PaymentModel();
        $this->queueNumberModel      = new QueueNumberModel();
        $this->tableModel            = new TableModel();
        $this->notifier              = new RealtimeNotifierService();
        $this->receiptPrinterService = new ReceiptPrinterService();
    }

    public function index()
    {
        $statusFilter = $this->request->getGet('status');

        $builder = $this->orderModel->select('orders.*, users.name as cashier_name, tables.table_number, queue_numbers.formatted_number as queue_number, payments.payment_method, payments.payment_provider')
                                    ->join('users', 'users.id = orders.user_id', 'left')
                                    ->join('tables', 'tables.id = orders.table_id', 'left')
                                    ->join('queue_numbers', 'queue_numbers.order_id = orders.id', 'left')
                                    ->join('payments', 'payments.order_id = orders.id', 'left')
                                    ->orderBy('orders.id', 'DESC');

        if (!empty($statusFilter)) {
            $builder->where('orders.status', $statusFilter);
        }

        $orders = $builder->findAll();

        foreach ($orders as &$ord) {
            $items = $this->orderItemModel->where('order_id', $ord['id'])->findAll();
            foreach ($items as &$item) {
                $item['toppings'] = $this->orderItemToppingModel->where('order_item_id', $item['id'])->findAll();
            }
            $ord['items'] = $items;
        }

        $data = [
            'title'        => 'Manajemen Pesanan',
            'orders'       => $orders,
            'activeFilter' => $statusFilter ?? 'all',
        ];

        return view('admin/orders/index', $data);
    }

    public function updateStatus($id = null)
    {
        $order = $this->orderModel->find($id);
        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        $newStatus = $this->request->getPost('status');
        $validStatuses = ['pending', 'diproses', 'siap', 'selesai', 'dibatalkan'];

        if (!in_array($newStatus, $validStatuses)) {
            return redirect()->to('/admin/orders')->with('error', 'Status pesanan tidak valid.');
        }

        if (in_array($newStatus, ['selesai', 'dibatalkan']) && !empty($order['table_id'])) {
            $this->tableModel->update($order['table_id'], ['status' => 'available']);
        }

        $this->orderModel->update($id, ['status' => $newStatus]);

        $this->notifier->broadcast('OrderStatusUpdated', [
            'order_id'   => $id,
            'new_status' => $newStatus,
        ]);

        // Catat ke Log Aktivitas
        log_activity('UPDATE_ORDER_STATUS', "Mengubah status pesanan #{$order['order_number']} menjadi " . strtoupper($newStatus));

        return redirect()->to('/admin/orders')->with('success', "Status pesanan {$order['order_number']} berhasil diubah menjadi " . strtoupper($newStatus));
    }

    /**
     * Tampilan Cetak Struk Thermal 58mm
     */
    public function printReceipt($id = null)
    {
        $order = $this->orderModel->find($id);
        $receiptContent = $this->receiptPrinterService->generateReceiptText((int)$id);

        if (empty($receiptContent)) {
            return "Struk tidak ditemukan.";
        }

        // Catat ke Log Aktivitas saat mencetak struk
        if ($order) {
            log_activity('PRINT_RECEIPT', "Mencetak struk pembayaran untuk pesanan #{$order['order_number']}");
        }

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Cetak Struk Thermal 58mm</title>
            <style>
                body {
                    font-family: 'Courier New', Courier, monospace;
                    width: 58mm;
                    margin: 0;
                    padding: 5px;
                    font-size: 11px;
                }
                pre {
                    margin: 0;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                }
                @media print {
                    @page { margin: 0; size: 58mm auto; }
                    body { margin: 0; }
                }
            </style>
        </head>
        <body onload='window.print();'>
            <pre>{$receiptContent}</pre>
        </body>
        </html>";
    }
}