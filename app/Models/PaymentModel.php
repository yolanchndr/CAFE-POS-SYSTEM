<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_id',
        'payment_method',
        'payment_provider',
        'amount_due',
        'amount_paid',
        'change_amount',
        'reference_number',
        'notes',
        'paid_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules = [
        'order_id'       => 'required|is_natural_no_zero|is_unique[payments.order_id,id,{id}]',
        'payment_method' => 'required|in_list[cash,non_cash]',
        'amount_due'     => 'required|numeric',
        'amount_paid'    => 'required|numeric',
        'paid_at'        => 'required',
    ];
}