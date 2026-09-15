<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'base_price',
        'is_available',
        'sort_order',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'category_id'  => 'required|is_natural_no_zero',
        'name'         => 'required|min_length[2]|max_length[150]',
        'slug'         => 'required|min_length[2]|max_length[150]|is_unique[products.slug,id,{id}]',
        'base_price'   => 'required|numeric|greater_than_equal_to[0]',
        'is_available' => 'in_list[0,1]',
    ];

    /**
     * Mengambil produk beserta nama kategorinya
     */
    public function getProductsWithCategory(?int $id = null)
    {
        $builder = $this->select('products.*, categories.name as category_name')
                        ->join('categories', 'categories.id = products.category_id', 'left');

        if ($id !== null) {
            return $builder->where('products.id', $id)->first();
        }

        return $builder->findAll();
    }
}