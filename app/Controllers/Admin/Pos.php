<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\ProductVariantModel;
use App\Models\ProductToppingModel;
use App\Models\TableModel;
use App\Services\OrderService;
use App\Services\PaymentService;
use Exception;

class Pos extends BaseController
{
    protected $categoryModel;
    protected $productModel;
    protected $variantModel;
    protected $productToppingModel;
    protected $tableModel;
    protected $orderService;
    protected $paymentService;

    public function __construct()
    {
        helper(['form', 'log']);
        $this->categoryModel       = new CategoryModel();
        $this->productModel        = new ProductModel();
        $this->variantModel        = new ProductVariantModel();
        $this->productToppingModel = new ProductToppingModel();
        $this->tableModel          = new TableModel();
        $this->orderService        = new OrderService();
        $this->paymentService      = new PaymentService();
    }

    public function index()
    {
        $data = [
            'title'        => 'POS / Kasir',
            'categories'   => $this->categoryModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll(),
            'products'     => $this->productModel->getProductsWithCategory(),
            'tables'       => $this->tableModel->where('is_active', 1)->orderBy('table_number', 'ASC')->findAll(),
            'tax_rate'     => (float) setting('tax_percentage', '10'),
            'service_rate' => (float) setting('service_charge_percentage', '0'),
        ];

        return view('admin/pos/index', $data);
    }

    public function getProductDetails($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->response->setJSON(['status' => false, 'message' => 'Produk tidak ditemukan']);
        }

        $variants = $this->variantModel->where('product_id', $id)
                                       ->where('is_available', 1)
                                       ->orderBy('sort_order', 'ASC')
                                       ->findAll();

        $toppings = $this->productToppingModel->getToppingsByProduct($id);

        return $this->response->setJSON([
            'status'   => true,
            'product'  => $product,
            'variants' => $variants,
            'toppings' => $toppings,
        ]);
    }

    public function checkout()
    {
        $json   = $this->request->getJSON(true);
        $userId = session()->get('user_id') ?? 1;

        try {
            $result = $this->orderService->createOrder($json, $userId);

            // Catat ke Log Aktivitas
            log_activity('CREATE_ORDER', "Kasir membuat pesanan baru #{$result['order_number']} (Total: Rp " . number_format($result['grand_total'], 0, ',', '.') . ")");

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Pesanan berhasil dibuat.',
                'data'    => $result,
            ]);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage(),
            ])->setStatusCode(400);
        }
    }

    public function processPayment()
    {
        $json = $this->request->getJSON(true);

        try {
            $result = $this->paymentService->processPayment($json);

            $paymentMethod   = strtoupper($json['payment_method'] ?? 'CASH');
            $paymentProvider = !empty($json['payment_provider']) ? " ({$json['payment_provider']})" : "";
            $orderNumber     = $result['order_number'] ?? "#{$json['order_id']}";

            // Catat ke Log Aktivitas
            log_activity('PROCESS_PAYMENT', "Pembayaran berhasil diproses untuk pesanan {$orderNumber} via {$paymentMethod}{$paymentProvider}");

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Pembayaran berhasil diproses.',
                'data'    => $result,
            ]);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage(),
            ])->setStatusCode(400);
        }
    }
}