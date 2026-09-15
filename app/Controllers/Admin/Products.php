<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ToppingModel;
use App\Models\ProductToppingModel;
use App\Services\RealtimeNotifierService;

class Products extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $toppingModel;
    protected $productToppingModel;
    protected $notifier;

    public function __construct()
    {
        helper(['text', 'form', 'log']);
        $this->productModel        = new ProductModel();
        $this->categoryModel       = new CategoryModel();
        $this->toppingModel        = new ToppingModel();
        $this->productToppingModel = new ProductToppingModel();
        $this->notifier            = new RealtimeNotifierService();
    }

    public function index()
    {
        $products = $this->productModel->getProductsWithCategory();

        // Ambil data relasi topping untuk setiap produk agar otomatis tercentang di modal edit
        foreach ($products as &$p) {
            $assignedToppings = $this->productToppingModel->where('product_id', $p['id'])->findAll();
            $p['assigned_toppings'] = array_column($assignedToppings, 'topping_id');
        }

        $data = [
            'title'      => 'Manajemen Produk / Menu',
            'products'   => $products,
            'categories' => $this->categoryModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll(),
            'toppings'   => $this->toppingModel->where('is_available', 1)->findAll(),
        ];
        return view('admin/products/index', $data);
    }

    public function store()
    {
        $rules = [
            'category_id' => 'required|is_natural_no_zero',
            'name'        => 'required|min_length[2]|max_length[150]',
            'base_price'  => 'required|numeric|greater_than_equal_to[0]',
            'image'       => 'is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|max_size[image,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $fileImage = $this->request->getFile('image');
        $imageName = null;

        if ($fileImage && $fileImage->isValid() && !$fileImage->hasMoved()) {
            $imageName = $fileImage->getRandomName();
            $fileImage->move('uploads/products', $imageName);
            $imageName = 'uploads/products/' . $imageName;
        }

        $name      = $this->request->getPost('name');
        $basePrice = $this->request->getPost('base_price');

        $productId = $this->productModel->insert([
            'category_id'  => $this->request->getPost('category_id'),
            'name'         => $name,
            'slug'         => url_title($name, '-', true) . '-' . time(),
            'description'  => $this->request->getPost('description'),
            'image'        => $imageName,
            'base_price'   => $basePrice,
            'is_available' => (int) $this->request->getPost('is_available'),
            'sort_order'   => (int) $this->request->getPost('sort_order'),
        ]);

        $toppings = $this->request->getPost('toppings');
        if (!empty($toppings) && is_array($toppings)) {
            foreach ($toppings as $toppingId) {
                $this->productToppingModel->insert([
                    'product_id' => $productId,
                    'topping_id' => $toppingId,
                ]);
            }
        }

        $this->notifier->broadcast('ProductAvailabilityUpdated', ['product_id' => $productId]);

        // Catat ke Log Aktivitas
        log_activity('CREATE_PRODUCT', "Menambahkan produk baru: '{$name}' (Harga: Rp " . number_format((float)$basePrice, 0, ',', '.') . ")");

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan.');
        }

        $rules = [
            'category_id' => 'required|is_natural_no_zero',
            'name'        => 'required|min_length[2]|max_length[150]',
            'base_price'  => 'required|numeric|greater_than_equal_to[0]',
            'image'       => 'is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|max_size[image,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $fileImage = $this->request->getFile('image');
        $imageName = $product['image'];

        if ($fileImage && $fileImage->isValid() && !$fileImage->hasMoved()) {
            if ($imageName && file_exists(FCPATH . $imageName)) {
                @unlink(FCPATH . $imageName);
            }
            $newName = $fileImage->getRandomName();
            $fileImage->move('uploads/products', $newName);
            $imageName = 'uploads/products/' . $newName;
        }

        $name = $this->request->getPost('name');
        $this->productModel->update($id, [
            'category_id'  => $this->request->getPost('category_id'),
            'name'         => $name,
            'slug'         => url_title($name, '-', true) . '-' . $id,
            'description'  => $this->request->getPost('description'),
            'image'        => $imageName,
            'base_price'   => $this->request->getPost('base_price'),
            'is_available' => (int) $this->request->getPost('is_available'),
            'sort_order'   => (int) $this->request->getPost('sort_order'),
        ]);

        $this->productToppingModel->where('product_id', $id)->delete();
        $toppings = $this->request->getPost('toppings');
        if (!empty($toppings) && is_array($toppings)) {
            foreach ($toppings as $toppingId) {
                $this->productToppingModel->insert([
                    'product_id' => $id,
                    'topping_id' => $toppingId,
                ]);
            }
        }

        $this->notifier->broadcast('ProductAvailabilityUpdated', ['product_id' => $id]);

        // Catat ke Log Aktivitas
        log_activity('UPDATE_PRODUCT', "Mengubah data produk ID #{$id} ({$name})");

        return redirect()->to('/admin/products')->with('success', 'Produk berhasil diperbarui.');
    }

    public function toggleAvailability($id = null)
    {
        $product = $this->productModel->find($id);
        if ($product) {
            $newStatus = $product['is_available'] == 1 ? 0 : 1;
            $this->productModel->update($id, ['is_available' => $newStatus]);

            $this->notifier->broadcast('ProductAvailabilityUpdated', ['product_id' => $id, 'is_available' => $newStatus]);

            $statusStr = $newStatus == 1 ? 'TERSEDIA' : 'HABIS';

            // Catat ke Log Aktivitas
            log_activity('TOGGLE_PRODUCT_STATUS', "Mengubah status disponibilitas produk '{$product['name']}' menjadi {$statusStr}");

            $msg = $newStatus == 1 ? 'Produk sekarang TERSEDIA.' : 'Produk sekarang HABIS.';
            return redirect()->to('/admin/products')->with('success', $msg);
        }
        return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan.');
    }

    public function delete($id = null)
    {
        $product = $this->productModel->find($id);
        if ($product) {
            $this->productModel->delete($id);

            // Catat ke Log Aktivitas
            log_activity('DELETE_PRODUCT', "Menghapus produk: '{$product['name']}' (ID #{$id})");

            $this->notifier->broadcast('ProductAvailabilityUpdated', ['product_id' => $id]);
            return redirect()->to('/admin/products')->with('success', 'Produk berhasil dihapus.');
        }

        return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan.');
    }
}