<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\OrderItemToppingModel;
use App\Models\ProductModel;
use App\Models\ProductVariantModel;
use App\Models\ToppingModel;
use App\Models\TableModel;
use App\Models\SettingModel;
use App\Services\RealtimeNotifierService;
use Exception;

class OrderService
{
    protected $db;
    protected $orderModel;
    protected $orderItemModel;
    protected $orderItemToppingModel;
    protected $productModel;
    protected $variantModel;
    protected $toppingModel;
    protected $tableModel;
    protected $settingModel;
    protected $notifier;

    public function __construct()
    {
        $this->db                    = \Config\Database::connect();
        $this->orderModel            = new OrderModel();
        $this->orderItemModel        = new OrderItemModel();
        $this->orderItemToppingModel = new OrderItemToppingModel();
        $this->productModel          = new ProductModel();
        $this->variantModel          = new ProductVariantModel();
        $this->toppingModel          = new ToppingModel();
        $this->tableModel            = new TableModel();
        $this->settingModel          = new SettingModel();
        $this->notifier              = new RealtimeNotifierService();
    }

    public function createOrder(array $payload, int $userId): array
    {
        if (empty($payload['cart']) || !is_array($payload['cart'])) {
            throw new Exception('Keranjang belanja kosong.');
        }

        $orderType = $payload['order_type'] ?? 'dine_in';
        $tableId   = (!empty($payload['table_id']) && $orderType === 'dine_in') ? (int) $payload['table_id'] : null;

        if ($orderType === 'dine_in' && !$tableId) {
            throw new Exception('Pilih meja terlebih dahulu untuk transaksi Dine-in.');
        }

        $this->db->transBegin();

        try {
            $orderNumber = $this->generateOrderNumber();
            $calculatedSubtotal = 0.00;
            $itemsToInsert      = [];

            foreach ($payload['cart'] as $item) {
                $productId = (int) $item['productId'];
                $product   = $this->productModel->find($productId);

                if (!$product) {
                    throw new Exception("Produk dengan ID {$productId} tidak ditemukan.");
                }

                if ((int) $product['is_available'] !== 1) {
                    throw new Exception("Produk '{$product['name']}' sedang HABIS dan tidak dapat dipesan.");
                }

                $basePrice   = (float) $product['base_price'];
                $variantId   = !empty($item['variantId']) ? (int) $item['variantId'] : null;
                $variantName = null;
                $variantPriceAdjustment = 0.00;

                if ($variantId) {
                    $variant = $this->variantModel->find($variantId);
                    if ($variant) {
                        $variantName            = $variant['name'];
                        $variantPriceAdjustment = (float) $variant['price_adjustment'];
                    }
                }

                $toppingsToInsert  = [];
                $totalToppingPrice = 0.00;

                if (!empty($item['toppings']) && is_array($item['toppings'])) {
                    foreach ($item['toppings'] as $topData) {
                        $toppingId = (int) $topData['id'];
                        $topping   = $this->toppingModel->find($toppingId);
                        if ($topping) {
                            $toppingPrice = (float) $topping['price'];
                            $totalToppingPrice += $toppingPrice;

                            $toppingsToInsert[] = [
                                'topping_id'   => $toppingId,
                                'topping_name' => $topping['name'],
                                'price'        => $toppingPrice,
                            ];
                        }
                    }
                }

                $qty          = (int) ($item['qty'] ?? 1);
                $unitPrice    = $basePrice + $variantPriceAdjustment + $totalToppingPrice;
                $itemSubtotal = $unitPrice * $qty;

                $calculatedSubtotal += $itemSubtotal;

                $itemsToInsert[] = [
                    'product_id'   => $productId,
                    'variant_id'   => $variantId,
                    'product_name' => $product['name'],
                    'variant_name' => $variantName,
                    'unit_price'   => $unitPrice,
                    'quantity'     => $qty,
                    'subtotal'     => $itemSubtotal,
                    'notes'        => $item['notes'] ?? null,
                    'toppings'     => $toppingsToInsert,
                ];
            }

            $taxPercentage     = (float) $this->settingModel->getByKey('tax_percentage', '10');
            $servicePercentage = (float) $this->settingModel->getByKey('service_charge_percentage', '0');
            $discount          = max(0.00, (float) ($payload['discount'] ?? 0));

            $taxAmount     = ($calculatedSubtotal * $taxPercentage) / 100;
            $serviceAmount = ($calculatedSubtotal * $servicePercentage) / 100;
            $grandTotal    = max(0.00, $calculatedSubtotal + $taxAmount + $serviceAmount - $discount);

            $orderData = [
                'order_number'   => $orderNumber,
                'user_id'        => $userId,
                'order_type'     => $orderType,
                'table_id'       => $tableId,
                'status'         => 'pending',
                'subtotal'       => $calculatedSubtotal,
                'discount'       => $discount,
                'tax'            => $taxAmount,
                'service_charge' => $serviceAmount,
                'grand_total'    => $grandTotal,
                'notes'          => $payload['notes'] ?? null,
            ];

            $orderId = $this->orderModel->insert($orderData);

            foreach ($itemsToInsert as $itemData) {
                $toppings = $itemData['toppings'];
                unset($itemData['toppings']);
                $itemData['order_id'] = $orderId;

                $orderItemId = $this->orderItemModel->insert($itemData);

                foreach ($toppings as $topData) {
                    $topData['order_item_id'] = $orderItemId;
                    $this->orderItemToppingModel->insert($topData);
                }
            }

            if ($tableId) {
                $this->tableModel->update($tableId, ['status' => 'occupied']);
            }

            $this->db->transCommit();

            // Broadcast Event Real-time OrderCreated
            $this->notifier->broadcast('OrderCreated', ['order_id' => $orderId, 'order_number' => $orderNumber]);

            return [
                'status'       => true,
                'order_id'     => $orderId,
                'order_number' => $orderNumber,
                'grand_total'  => $grandTotal,
            ];
        } catch (Exception $e) {
            $this->db->transRollback();
            throw new Exception($e->getMessage());
        }
    }

    private function generateOrderNumber(): string
    {
        $prefix = date('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return 'INV-' . $prefix . '-' . $random;
    }
}