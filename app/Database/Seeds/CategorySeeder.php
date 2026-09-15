<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Coffee', 'slug' => 'coffee', 'sort_order' => 1],
            ['name' => 'Non Coffee', 'slug' => 'non-coffee', 'sort_order' => 2],
            ['name' => 'Tea', 'slug' => 'tea', 'sort_order' => 3],
            ['name' => 'Food', 'slug' => 'food', 'sort_order' => 4],
            ['name' => 'Snack', 'slug' => 'snack', 'sort_order' => 5],
            ['name' => 'Dessert', 'slug' => 'dessert', 'sort_order' => 6],
        ];

        foreach ($categories as $cat) {
            $cat['is_active']  = 1;
            $cat['created_at'] = date('Y-m-d H:i:s');
            $cat['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('categories')->ignore(true)->insert($cat);
        }
    }
}