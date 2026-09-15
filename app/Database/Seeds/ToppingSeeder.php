<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ToppingSeeder extends Seeder
{
    public function run()
    {
        $toppings = [
            ['name' => 'Extra Shot Espresso', 'price' => 5000.00],
            ['name' => 'Cream Cheese', 'price' => 4000.00],
            ['name' => 'Boba Brown Sugar', 'price' => 3000.00],
            ['name' => 'Grass Jelly', 'price' => 3000.00],
        ];

        foreach ($toppings as $top) {
            $top['is_available'] = 1;
            $top['created_at']   = date('Y-m-d H:i:s');
            $top['updated_at']   = date('Y-m-d H:i:s');
            $this->db->table('toppings')->ignore(true)->insert($top);
        }
    }
}