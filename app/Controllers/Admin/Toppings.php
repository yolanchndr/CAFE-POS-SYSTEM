<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ToppingModel;

class Toppings extends BaseController
{
    protected $toppingModel;

    public function __construct()
    {
        helper(['form', 'log']);
        $this->toppingModel = new ToppingModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Manajemen Topping',
            'toppings' => $this->toppingModel->findAll(),
        ];
        return view('admin/toppings/index', $data);
    }

    public function store()
    {
        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'price' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $name  = $this->request->getPost('name');
        $price = $this->request->getPost('price');

        $this->toppingModel->save([
            'name'         => $name,
            'price'        => $price,
            'is_available' => (int) $this->request->getPost('is_available'),
        ]);

        // Catat ke Log Aktivitas
        log_activity('CREATE_TOPPING', "Menambahkan topping baru: '{$name}' (Harga: Rp " . number_format((float)$price, 0, ',', '.') . ")");

        return redirect()->to('/admin/toppings')->with('success', 'Topping berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        $topping = $this->toppingModel->find($id);
        if (!$topping) {
            return redirect()->to('/admin/toppings')->with('error', 'Topping tidak ditemukan.');
        }

        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'price' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $name  = $this->request->getPost('name');
        $price = $this->request->getPost('price');

        $this->toppingModel->update($id, [
            'name'         => $name,
            'price'        => $price,
            'is_available' => (int) $this->request->getPost('is_available'),
        ]);

        // Catat ke Log Aktivitas
        log_activity('UPDATE_TOPPING', "Mengubah data topping ID #{$id} ('{$name}')");

        return redirect()->to('/admin/toppings')->with('success', 'Topping berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $topping = $this->toppingModel->find($id);
        if ($topping) {
            $this->toppingModel->delete($id);

            // Catat ke Log Aktivitas
            log_activity('DELETE_TOPPING', "Menghapus topping: '{$topping['name']}' (ID #{$id})");

            return redirect()->to('/admin/toppings')->with('success', 'Topping berhasil dihapus.');
        }

        return redirect()->to('/admin/toppings')->with('error', 'Topping tidak ditemukan.');
    }
}