<?php

namespace App\Models;

use CodeIgniter\Model;

class TableModel extends Model
{
    protected $table            = 'tables';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'table_number',
        'name',
        'capacity',
        'status',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'table_number' => 'required|min_length[1]|max_length[20]|is_unique[tables.table_number,id,{id}]',
        'name'         => 'required|min_length[1]|max_length[50]',
        'capacity'     => 'required|is_natural_no_zero',
        'status'       => 'required|in_list[available,occupied,reserved,inactive]',
    ];
}