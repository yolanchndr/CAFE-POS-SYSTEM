<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run()
    {
        $tables = [
            ['table_number' => 'M01', 'name' => 'Meja 01', 'capacity' => 2],
            ['table_number' => 'M02', 'name' => 'Meja 02', 'capacity' => 2],
            ['table_number' => 'M03', 'name' => 'Meja 03', 'capacity' => 4],
            ['table_number' => 'M04', 'name' => 'Meja 04', 'capacity' => 4],
            ['table_number' => 'M05', 'name' => 'Meja VIP 01', 'capacity' => 6],
        ];

        foreach ($tables as $tbl) {
            $tbl['status']     = 'available';
            $tbl['is_active']  = 1;
            $tbl['created_at'] = date('Y-m-d H:i:s');
            $tbl['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('tables')->ignore(true)->insert($tbl);
        }
    }
}