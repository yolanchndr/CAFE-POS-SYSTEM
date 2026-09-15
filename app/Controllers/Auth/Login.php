<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SettingModel;

class Login extends BaseController
{
    protected $userModel;
    protected $settingModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/admin/dashboard');
        }

        $data = [
            'title'     => 'Login Admin - ' . ($this->settingModel->getByKey('app_name', 'Senja POS Cafe')),
            'cafe_name' => $this->settingModel->getByKey('cafe_name', 'Kopi Senja Utama'),
            'cafe_logo' => $this->settingModel->getByKey('cafe_logo', 'assets/images/logo.png'),
        ];

        return view('auth/login', $data);
    }

    public function process()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username atau password tidak ditemukan.');
        }

        if ((int)$user['is_active'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Akun Anda telah dinonaktifkan. Hubungi admin.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        // Set Data Session
        $sessionData = [
            'user_id'   => $user['id'],
            'name'      => $user['name'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'logged_in' => true,
        ];

        session()->set($sessionData);
        session()->setFlashdata('success', 'Selamat datang kembali, ' . $user['name'] . '!');

        return redirect()->to('/admin/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }
}