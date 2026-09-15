<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\OrderItemToppingModel;
use App\Models\PaymentModel;
use App\Models\QueueNumberModel;

class ReceiptPrinterService
{
    protected $orderModel;
    protected $orderItemModel;
    protected $orderItemToppingModel;
    protected $paymentModel;
    protected $queueNumberModel;

    public function __construct()
    {
        $this->orderModel            = new OrderModel();
        $this->orderItemModel        = new OrderItemModel();
        $this->orderItemToppingModel = new OrderItemToppingModel();
        $this->paymentModel          = new PaymentModel();
        $this->queueNumberModel      = new QueueNumberModel();
    }

    /**
     * Formatting Struk Thermal 58mm (Lebar Standar: 32 Karakter Monospace)
     */
    public function generateReceiptText(int $orderId): string
    {
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            return '';
        }

        $items   = $this->orderItemModel->where('order_id', $orderId)->findAll();
        $payment = $this->paymentModel->where('order_id', $orderId)->first();
        $queue   = $this->queueNumberModel->where('order_id', $orderId)->first();

        // Identitas Dinamis dari Settings Database
        $cafeName    = setting('receipt_header_name', setting('cafe_name', 'KOPI SENJA UTAMA'));
        $cafeAddress = setting('receipt_address', setting('cafe_address', 'Jl. Kopi Senja No. 88'));
        $cafePhone   = setting('receipt_phone', setting('cafe_phone', '0812-3456-7890'));
        $footerText  = setting('receipt_footer', "TERIMA KASIH\nSelamat menikmati");

        $width = 32; // Standard 58mm width
        $divider = str_repeat('-', $width) . "\n";

        $output  = "";
        $output .= $this->centerText(strtoupper($cafeName), $width) . "\n";
        $output .= $this->centerText($cafeAddress, $width) . "\n";
        $output .= $this->centerText("Telp: " . $cafePhone, $width) . "\n";
        $output .= $divider;

        $output .= $this->formatRow("Invoice", ": " . $order['order_number'], $width);
        if ($queue) {
            $output .= $this->formatRow("Antrian", ": " . $queue['formatted_number'], $width);
        }
        $output .= $this->formatRow("Tanggal", ": " . date('d/m/Y H:i', strtotime($order['created_at'])), $width);
        $output .= $this->formatRow("Tipe", ": " . strtoupper($order['order_type']), $width);
        $output .= $divider;

        // Items List
        foreach ($items as $item) {
            $nameStr = $item['quantity'] . 'x ' . $item['product_name'];
            if (!empty($item['variant_name'])) {
                $nameStr .= ' (' . $item['variant_name'] . ')';
            }
            
            $subtotalStr = 'Rp ' . number_format($item['subtotal'], 0, ',', '.');
            $output .= $this->formatRow($nameStr, $subtotalStr, $width);

            // Print toppings
            $toppings = $this->orderItemToppingModel->where('order_item_id', $item['id'])->findAll();
            if (!empty($toppings)) {
                foreach ($toppings as $top) {
                    $output .= $this->formatRow('  + ' . $top['topping_name'], 'Rp ' . number_format($top['price'], 0, ',', '.'), $width);
                }
            }

            if (!empty($item['notes'])) {
                $output .= "  *" . $item['notes'] . "\n";
            }
        }

        $output .= $divider;
        $output .= $this->formatRow("Subtotal", "Rp " . number_format($order['subtotal'], 0, ',', '.'), $width);
        if ($order['tax'] > 0) {
            $output .= $this->formatRow("Pajak", "Rp " . number_format($order['tax'], 0, ',', '.'), $width);
        }
        if ($order['discount'] > 0) {
            $output .= $this->formatRow("Diskon", "-Rp " . number_format($order['discount'], 0, ',', '.'), $width);
        }
        $output .= $this->formatRow("TOTAL", "Rp " . number_format($order['grand_total'], 0, ',', '.'), $width);
        $output .= $divider;

        if ($payment) {
            $output .= $this->formatRow("Metode", strtoupper($payment['payment_method']), $width);
            $output .= $this->formatRow("Bayar", "Rp " . number_format($payment['amount_paid'], 0, ',', '.'), $width);
            $output .= $this->formatRow("Kembali", "Rp " . number_format($payment['change_amount'], 0, ',', '.'), $width);
            $output .= $divider;
        }

        $lines = explode("\n", $footerText);
        foreach ($lines as $line) {
            $output .= $this->centerText($line, $width) . "\n";
        }

        return $output;
    }

    private function centerText(string $text, int $width): string
    {
        $len = strlen($text);
        if ($len >= $width) return substr($text, 0, $width);
        $left = floor(($width - $len) / 2);
        return str_repeat(' ', $left) . $text;
    }

    private function formatRow(string $left, string $right, int $width): string
    {
        $rightLen = strlen($right);
        $maxLeftLen = $width - $rightLen - 1;

        if (strlen($left) > $maxLeftLen) {
            $left = substr($left, 0, $maxLeftLen);
        }

        $spaces = $width - strlen($left) - $rightLen;
        return $left . str_repeat(' ', max(1, $spaces)) . $right . "\n";
    }
}