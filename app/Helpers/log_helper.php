<?php

if (!function_exists('log_activity')) {
    /**
     * Mencatat aktivitas pengguna ke tabel activity_logs
     *
     * @param string $action Nama aksi/event (misal: CREATE_CATEGORY, UPDATE_PRODUCT)
     * @param string $description Detail deskripsi aktivitas
     * @return bool
     */
    function log_activity(string $action, string $description): bool
    {
        $db = \Config\Database::connect();
        $request = \Config\Services::request();

        if (!$db->tableExists('activity_logs')) {
            return false;
        }

        return $db->table('activity_logs')->insert([
            'user_id'     => session()->get('user_id') ?? 1,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => $request->getIPAddress(),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}