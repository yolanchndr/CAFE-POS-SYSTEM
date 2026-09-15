<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_number',
        'user_id',
        'order_type',
        'table_id',
        'status',
        'subtotal',
        'discount',
        'tax',
        'service_charge',
        'grand_total',
        'notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'order_number'   => 'required|is_unique[orders.order_number,id,{id}]',
        'user_id'        => 'required|is_natural_no_zero',
        'order_type'     => 'required|in_list[dine_in,takeaway]',
        'status'         => 'required|in_list[pending,diproses,siap,selesai,dibatalkan]',
        'subtotal'       => 'required|numeric',
        'grand_total'    => 'required|numeric',
    ];
}