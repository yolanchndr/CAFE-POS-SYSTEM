<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use App\Models\TableModel;

class Dashboard extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $productModel;
    protected $tableModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->productModel   = new ProductModel();
        $this->tableModel     = new TableModel();
    }

    public function index()
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        // 1. Stat Cards
        $todayRevenue = $this->orderModel->selectSum('grand_total')
                                         ->where('status', 'selesai')
                                         ->like('created_at', $today)
                                         ->first()['grand_total'] ?? 0;

        $monthRevenue = $this->orderModel->selectSum('grand_total')
                                         ->where('status', 'selesai')
                                         ->like('created_at', $thisMonth)
                                         ->first()['grand_total'] ?? 0;

        $todayOrders = $this->orderModel->where('status', 'selesai')
                                        ->like('created_at', $today)
                                        ->countAllResults();

        $occupiedTables = $this->tableModel->where('status', 'occupied')->countAllResults();
        $totalTables    = $this->tableModel->where('is_active', 1)->countAllResults();

        // 2. Best Sellers
        $bestSellers = $this->orderItemModel->select('product_name, SUM(quantity) as total_qty')
                                            ->groupBy('product_name')
                                            ->orderBy('total_qty', 'DESC')
                                            ->limit(5)
                                            ->findAll();

        // 3. Last 7 Days Revenue Chart Data
        $chartDates = [];
        $chartData  = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chartDates[] = date('d M', strtotime($date));

            $rev = $this->orderModel->selectSum('grand_total')
                                    ->where('status', 'selesai')
                                    ->like('created_at', $date)
                                    ->first()['grand_total'] ?? 0;
            $chartData[] = (float) $rev;
        }

        $data = [
            'title'          => 'Dashboard Admin',
            'todayRevenue'   => $todayRevenue,
            'monthRevenue'   => $monthRevenue,
            'todayOrders'    => $todayOrders,
            'occupiedTables' => $occupiedTables,
            'totalTables'    => $totalTables,
            'bestSellers'    => $bestSellers,
            'chartDates'     => $chartDates,
            'chartData'      => $chartData,
        ];

        return view('admin/dashboard/index', $data);
    }

    public function reports()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?? date('Y-m-d');

        $orders = $this->orderModel->select('orders.*, payments.payment_method, payments.payment_provider')
                                   ->join('payments', 'payments.order_id = orders.id', 'left')
                                   ->where('orders.status', 'selesai')
                                   ->where("DATE(orders.created_at) >=", $startDate)
                                   ->where("DATE(orders.created_at) <=", $endDate)
                                   ->orderBy('orders.id', 'DESC')
                                   ->findAll();

        $totalRevenue = array_sum(array_column($orders, 'grand_total'));
        $totalOrders  = count($orders);

        $data = [
            'title'        => 'Laporan Penjualan',
            'orders'       => $orders,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalOrders'  => $totalOrders,
        ];

        return view('admin/reports/index', $data);
    }
}