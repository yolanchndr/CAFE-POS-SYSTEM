<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // Identitas Cafe
            [
                'setting_key'   => 'cafe_name',
                'setting_value' => 'Kopi Senja Utama',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'cafe_description',
                'setting_value' => 'Tempat Nongkrong Kopi Paling Nyaman',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'cafe_phone',
                'setting_value' => '0812-3456-7890',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'cafe_email',
                'setting_value' => 'info@kopisenja.com',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'cafe_address',
                'setting_value' => 'Jl. Kopi Senja No. 88, Jakarta',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'cafe_logo',
                'setting_value' => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'cafe_favicon',
                'setting_value' => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],

            // Transaksi & Pajak
            [
                'setting_key'   => 'tax_percentage',
                'setting_value' => '10',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'service_charge_percentage',
                'setting_value' => '0',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],

            // Nomor Antrian
            [
                'setting_key'   => 'queue_prefix',
                'setting_value' => 'A',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'queue_digit_length',
                'setting_value' => '3',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],

            // Struk Thermal 58mm
            [
                'setting_key'   => 'receipt_header_name',
                'setting_value' => 'KOPI SENJA UTAMA',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'receipt_address',
                'setting_value' => 'Jl. Kopi Senja No. 88, Jakarta',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'receipt_phone',
                'setting_value' => '0812-3456-7890',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'receipt_footer',
                'setting_value' => "TERIMA KASIH\nSelamat menikmati pesanan Anda!",
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        // Gunakan upsert batch agar tidak error jika data seeder sudah ada di database
        $this->db->table('settings')->upsertBatch($settings);
    }
}