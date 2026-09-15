<?php

namespace App\Models;

use CodeIgniter\Model;

class ToppingModel extends Model
{
    protected $table            = 'toppings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'price',
        'is_available',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name'  => 'required|min_length[2]|max_length[100]',
        'price' => 'required|numeric|greater_than_equal_to[0]',
    ];
}