<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'category_id'  => 1,
                'name'         => 'Es Kopi Susu Senja',
                'slug'         => 'es-kopi-susu-senja',
                'description'  => 'Espresso dengan susu segar dan gula aren pilihan',
                'base_price'   => 18000.00,
                'is_available' => 1,
                'sort_order'   => 1,
            ],
            [
                'category_id'  => 1,
                'name'         => 'Americano Hot/Ice',
                'slug'         => 'americano',
                'description'  => 'Double shot espresso dengan air murni',
                'base_price'   => 15000.00,
                'is_available' => 1,
                'sort_order'   => 2,
            ],
            [
                'category_id'  => 2,
                'name'         => 'Matcha Latte',
                'slug'         => 'matcha-latte',
                'description'  => 'Bubuk matcha jepang asli dicampur susu creamy',
                'base_price'   => 22000.00,
                'is_available' => 1,
                'sort_order'   => 3,
            ],
            [
                'category_id'  => 5,
                'name'         => 'Butter Croissant',
                'slug'         => 'butter-croissant',
                'description'  => 'Croissant renyah dengan mentega premium',
                'base_price'   => 17000.00,
                'is_available' => 1,
                'sort_order'   => 4,
            ],
        ];

        foreach ($products as $prod) {
            $prod['created_at'] = date('Y-m-d H:i:s');
            $prod['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('products')->ignore(true)->insert($prod);
        }
    }
}