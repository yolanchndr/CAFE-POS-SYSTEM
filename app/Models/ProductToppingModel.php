<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductToppingModel extends Model
{
    protected $table            = 'product_toppings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'topping_id',
    ];

    protected $useTimestamps = false;

    /**
     * Mengambil daftar topping yang terhubung dengan suatu produk
     */
    public function getToppingsByProduct(int $productId)
    {
        return $this->select('toppings.*')
                    ->join('toppings', 'toppings.id = product_toppings.topping_id')
                    ->where('product_toppings.product_id', $productId)
                    ->where('toppings.is_available', 1)
                    ->findAll();
    }
}