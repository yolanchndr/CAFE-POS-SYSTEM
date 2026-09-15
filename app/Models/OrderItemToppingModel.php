<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemToppingModel extends Model
{
    protected $table            = 'order_item_toppings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_item_id',
        'topping_id',
        'topping_name',
        'price',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}