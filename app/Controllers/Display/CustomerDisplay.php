<?php

namespace App\Controllers\Display;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\QueueNumberModel;

class CustomerDisplay extends BaseController
{
    protected $categoryModel;
    protected $productModel;
    protected $orderModel;
    protected $orderItemModel;
    protected $queueNumberModel;

    public function __construct()
    {
        $this->categoryModel    = new CategoryModel();
        $this->productModel     = new ProductModel();
        $this->orderModel       = new OrderModel();
        $this->orderItemModel   = new OrderItemModel();
        $this->queueNumberModel = new QueueNumberModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Customer Display - ' . setting('cafe_name', 'Kopi Senja Utama'),
            'categories' => $this->categoryModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll(),
            'products'   => $this->productModel->getProductsWithCategory(),
        ];

        return view('display/customer/index', $data);
    }

    /**
     * API Endpoint READ-ONLY untuk mengambil transaksi aktif terupdate yang sedang dikerjakan Kasir
     */
    public function getActiveOrder()
    {
        // Mengambil pesanan terbaru berstatus pending / diproses dalam 10 menit terakhir
        $activeOrder = $this->orderModel->select('orders.*, queue_numbers.formatted_number as queue_number')
                                         ->join('queue_numbers', 'queue_numbers.order_id = orders.id', 'left')
                                         ->whereIn('orders.status', ['pending', 'diproses'])
                                         ->orderBy('orders.id', 'DESC')
                                         ->first();

        if (!$activeOrder) {
            return $this->response->setJSON(['status' => false, 'data' => null]);
        }

        $items = $this->orderItemModel->where('order_id', $activeOrder['id'])->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data'   => [
                'order_number' => $activeOrder['order_number'],
                'queue_number' => $activeOrder['queue_number'] ?? '-',
                'order_type'   => strtoupper($activeOrder['order_type']),
                'subtotal'     => (float) $activeOrder['subtotal'],
                'discount'     => (float) $activeOrder['discount'],
                'tax'          => (float) $activeOrder['tax'],
                'grand_total'  => (float) $activeOrder['grand_total'],
                'status'       => strtoupper($activeOrder['status']),
                'items'        => $items,
            ]
        ]);
    }
}