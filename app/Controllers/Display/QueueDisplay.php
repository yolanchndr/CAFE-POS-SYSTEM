<?php

namespace App\Controllers\Display;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\QueueNumberModel;

class QueueDisplay extends BaseController
{
    protected $orderModel;
    protected $queueNumberModel;

    public function __construct()
    {
        $this->orderModel       = new OrderModel();
        $this->queueNumberModel = new QueueNumberModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Queue Display - ' . setting('cafe_name', 'Kopi Senja Utama'),
        ];

        return view('display/queue/index', $data);
    }

    /**
     * API Endpoint READ-ONLY untuk mengambil data antrian SEDANG DISIAPKAN & PESANAN SIAP
     */
    public function getQueueData()
    {
        $today = date('Y-m-d');

        // 1. Pesanan SEDANG DISIAPKAN (status = 'diproses')
        $preparing = $this->orderModel->select('queue_numbers.formatted_number as queue_number')
                                       ->join('queue_numbers', 'queue_numbers.order_id = orders.id')
                                       ->where('orders.status', 'diproses')
                                       ->where('queue_numbers.queue_date', $today)
                                       ->orderBy('orders.id', 'ASC')
                                       ->findAll();

        // 2. Pesanan SUDAH SIAP (status = 'siap')
        $ready = $this->orderModel->select('queue_numbers.formatted_number as queue_number')
                                   ->join('queue_numbers', 'queue_numbers.order_id = orders.id')
                                   ->where('orders.status', 'siap')
                                   ->where('queue_numbers.queue_date', $today)
                                   ->orderBy('orders.id', 'ASC')
                                   ->findAll();

        return $this->response->setJSON([
            'status'    => true,
            'preparing' => array_column($preparing, 'queue_number'),
            'ready'     => array_column($ready, 'queue_number'),
        ]);
    }
}