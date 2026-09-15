<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Settings extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        helper(['form', 'log']);
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        $allSettings = $this->settingModel->findAll();
        $settingsMap = [];
        foreach ($allSettings as $s) {
            $settingsMap[$s['setting_key']] = $s['setting_value'];
        }

        $data = [
            'title'    => 'Pengaturan Sistem',
            'settings' => $settingsMap,
        ];

        return view('admin/settings/index', $data);
    }

    public function update()
    {
        $posts = $this->request->getPost();

        // Simpan atau Perbarui Setiap Setting Key
        foreach ($posts as $key => $value) {
            if ($key === 'csrf_test_name') continue;
            $this->settingModel->setByKey($key, is_array($value) ? json_encode($value) : $value);
        }

        // Upload Logo / Favicon jika ada
        $logo = $this->request->getFile('cafe_logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $logoName = 'logo_' . time() . '.' . $logo->getExtension();
            $logo->move('uploads/settings', $logoName);
            $this->settingModel->setByKey('cafe_logo', 'uploads/settings/' . $logoName);
        }

        $favicon = $this->request->getFile('cafe_favicon');
        if ($favicon && $favicon->isValid() && !$favicon->hasMoved()) {
            $favName = 'favicon_' . time() . '.' . $favicon->getExtension();
            $favicon->move('uploads/settings', $favName);
            $this->settingModel->setByKey('cafe_favicon', 'uploads/settings/' . $favName);
        }

        // Catat ke Log Aktivitas
        log_activity('UPDATE_SETTINGS', 'Memperbarui konfigurasi & pengaturan sistem cafe.');

        return redirect()->to('/admin/settings')->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}