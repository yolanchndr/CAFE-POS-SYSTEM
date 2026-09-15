<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductVariantModel;
use App\Models\ProductModel;

class Variants extends BaseController
{
    protected $variantModel;
    protected $productModel;

    public function __construct()
    {
        helper(['form', 'log']);
        $this->variantModel = new ProductVariantModel();
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $data = [
            'title'    => 'Manajemen Varian',
            'variants' => $this->variantModel->select('product_variants.*, products.name as product_name')
                                             ->join('products', 'products.id = product_variants.product_id')
                                             ->orderBy('products.name', 'ASC')
                                             ->findAll(),
            'products' => $this->productModel->findAll(),
        ];
        return view('admin/variants/index', $data);
    }

    public function store()
    {
        $rules = [
            'product_id'       => 'required|is_natural_no_zero',
            'name'             => 'required|min_length[1]|max_length[100]',
            'price_adjustment' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $productId       = $this->request->getPost('product_id');
        $name            = $this->request->getPost('name');
        $priceAdjustment = $this->request->getPost('price_adjustment');

        $this->variantModel->save([
            'product_id'       => $productId,
            'name'             => $name,
            'price_adjustment' => $priceAdjustment,
            'is_available'     => (int) $this->request->getPost('is_available'),
            'sort_order'       => (int) $this->request->getPost('sort_order'),
        ]);

        $product = $this->productModel->find($productId);
        $productName = $product ? $product['name'] : 'Unknown Product';

        // Catat ke Log Aktivitas
        log_activity('CREATE_VARIANT', "Menambahkan varian baru: '{$name}' pada produk '{$productName}'");

        return redirect()->to('/admin/variants')->with('success', 'Varian berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        $variant = $this->variantModel->find($id);
        if (!$variant) {
            return redirect()->to('/admin/variants')->with('error', 'Varian tidak ditemukan.');
        }

        $rules = [
            'product_id'       => 'required|is_natural_no_zero',
            'name'             => 'required|min_length[1]|max_length[100]',
            'price_adjustment' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $productId       = $this->request->getPost('product_id');
        $name            = $this->request->getPost('name');
        $priceAdjustment = $this->request->getPost('price_adjustment');

        $this->variantModel->update($id, [
            'product_id'       => $productId,
            'name'             => $name,
            'price_adjustment' => $priceAdjustment,
            'is_available'     => (int) $this->request->getPost('is_available'),
            'sort_order'       => (int) $this->request->getPost('sort_order'),
        ]);

        $product = $this->productModel->find($productId);
        $productName = $product ? $product['name'] : 'Unknown Product';

        // Catat ke Log Aktivitas
        log_activity('UPDATE_VARIANT', "Mengubah data varian ID #{$id} ('{$name}' - produk '{$productName}')");

        return redirect()->to('/admin/variants')->with('success', 'Varian berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $variant = $this->variantModel->find($id);
        if ($variant) {
            $this->variantModel->delete($id);

            // Catat ke Log Aktivitas
            log_activity('DELETE_VARIANT', "Menghapus varian: '{$variant['name']}' (ID #{$id})");

            return redirect()->to('/admin/variants')->with('success', 'Varian berhasil dihapus.');
        }

        return redirect()->to('/admin/variants')->with('error', 'Varian tidak ditemukan.');
    }
}