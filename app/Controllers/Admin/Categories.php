<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        // Load helper form, text (untuk url_title), dan log
        helper(['form', 'text', 'log']);
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Manajemen Kategori',
            'categories' => $this->categoryModel->orderBy('sort_order', 'ASC')->findAll(),
        ];
        return view('admin/categories/index', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('error', 'Gagal menambahkan kategori. Periksa inputan kamu.');
        }

        $name = trim($this->request->getPost('name'));

        // 1. Cek apakah ada kategori aktif (belum terhapus) dengan nama yang sama
        $activeCategory = $this->categoryModel->where('name', $name)->first();
        if ($activeCategory) {
            return redirect()->back()->withInput()->with('errors', ['name' => 'Nama kategori sudah digunakan.'])->with('error', 'Nama kategori sudah ada.');
        }

        // 2. Jika ada record lama di Soft Delete dengan nama sama, hapus permanen (purge)
        $existingDeleted = $this->categoryModel->onlyDeleted()->where('name', $name)->first();
        if ($existingDeleted) {
            $this->categoryModel->delete($existingDeleted['id'], true);
        }

        $inserted = $this->categoryModel->insert([
            'name'       => $name,
            'slug'       => url_title($name, '-', true),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_active'  => (int) $this->request->getPost('is_active'),
        ]);

        if (!$inserted) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan kategori ke database.');
        }

        // --- CATAT KE LOG AKTIVITAS ---
        log_activity('CREATE_CATEGORY', "Menambahkan kategori baru: '{$name}'");

        return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors())->with('error', 'Gagal memperbarui kategori.');
        }

        $name = trim($this->request->getPost('name'));

        // Cek duplikasi nama pada kategori lain yang masih aktif
        $duplicate = $this->categoryModel->where('name', $name)->where('id !=', $id)->first();
        if ($duplicate) {
            return redirect()->back()->withInput()->with('errors', ['name' => 'Nama kategori sudah digunakan oleh kategori lain.'])->with('error', 'Nama kategori sudah ada.');
        }

        $this->categoryModel->update($id, [
            'name'       => $name,
            'slug'       => url_title($name, '-', true),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_active'  => (int) $this->request->getPost('is_active'),
        ]);

        // --- CATAT KE LOG AKTIVITAS ---
        log_activity('UPDATE_CATEGORY', "Mengubah data kategori ID #{$id} ({$name})");

        return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $category = $this->categoryModel->find($id);
        if ($category) {
            $this->categoryModel->delete($id);

            // --- CATAT KE LOG AKTIVITAS ---
            log_activity('DELETE_CATEGORY', "Menghapus kategori: '{$category['name']}' (ID #{$id})");

            return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil dihapus.');
        }

        return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan.');
    }
}