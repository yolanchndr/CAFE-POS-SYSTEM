<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ActivityLogs extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Mengecek apakah tabel activity_logs ada di database
        if (!$this->db->tableExists('activity_logs')) {
            $logs = [];
        } else {
            $logs = $this->db->table('activity_logs')
                             ->select('activity_logs.*, users.name as user_name')
                             ->join('users', 'users.id = activity_logs.user_id', 'left')
                             ->orderBy('activity_logs.id', 'DESC')
                             ->get()
                             ->getResultArray();
        }

        $data = [
            'title' => 'Log Aktivitas Sistem',
            'logs'  => $logs,
        ];

        return view('admin/activity_logs/index', $data);
    }
}