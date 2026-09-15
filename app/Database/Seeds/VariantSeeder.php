<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class VariantSeeder extends Seeder
{
    public function run()
    {
        $variants = [
            ['product_id' => 1, 'name' => 'Regular', 'price_adjustment' => 0.00, 'sort_order' => 1],
            ['product_id' => 1, 'name' => 'Large', 'price_adjustment' => 5000.00, 'sort_order' => 2],
            ['product_id' => 3, 'name' => 'Regular', 'price_adjustment' => 0.00, 'sort_order' => 1],
            ['product_id' => 3, 'name' => 'Large', 'price_adjustment' => 4000.00, 'sort_order' => 2],
        ];

        foreach ($variants as $var) {
            $var['is_available'] = 1;
            $var['created_at']   = date('Y-m-d H:i:s');
            $var['updated_at']   = date('Y-m-d H:i:s');
            $this->db->table('product_variants')->ignore(true)->insert($var);
        }
    }
}