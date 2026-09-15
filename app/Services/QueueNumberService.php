<?php

namespace App\Services;

use App\Models\QueueNumberModel;
use App\Models\SettingModel;
use Exception;

class QueueNumberService
{
    protected $db;
    protected $queueNumberModel;
    protected $settingModel;

    public function __construct()
    {
        $this->db               = \Config\Database::connect();
        $this->queueNumberModel = new QueueNumberModel();
        $this->settingModel     = new SettingModel();
    }

    /**
     * Generate Nomor Antrian Baru yang Aman Dari Concurrency / Duplicate Entry.
     *
     * @param int $orderId ID Transaksi Order
     * @return array Data antrian yang dibuat
     */
    public function generateQueueNumber(int $orderId): array
    {
        $today = date('Y-m-d');

        // Cek jika order ini sudah punya nomor antrian
        $existing = $this->queueNumberModel->where('order_id', $orderId)->first();
        if ($existing) {
            return $existing;
        }

        // Lock baris transaksi harian menggunakan raw Query dengan FOR UPDATE
        $query = $this->db->query(
            "SELECT MAX(queue_number) AS max_num FROM queue_numbers WHERE queue_date = ? FOR UPDATE",
            [$today]
        );
        $row = $query->getRowArray();
        $nextNumber = ((int) ($row['max_num'] ?? 0)) + 1;

        // Prefix & Lenght dari Database Settings
        $prefix = $this->settingModel->getByKey('queue_prefix', 'A');
        $length = (int) $this->settingModel->getByKey('queue_digit_length', '3');
        $formatted = $prefix . str_pad((string)$nextNumber, $length, '0', STR_PAD_LEFT);

        $data = [
            'order_id'         => $orderId,
            'queue_date'       => $today,
            'queue_number'     => $nextNumber,
            'formatted_number' => $formatted,
        ];

        $this->queueNumberModel->insert($data);

        return $data;
    }
}