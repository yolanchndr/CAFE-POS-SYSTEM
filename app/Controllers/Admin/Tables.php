<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TableModel;

class Tables extends BaseController
{
    protected $tableModel;

    public function __construct()
    {
        helper(['form', 'log']);
        $this->tableModel = new TableModel();
    }

    public function index()
    {
        $data = [
            'title'  => 'Manajemen Meja',
            'tables' => $this->tableModel->orderBy('table_number', 'ASC')->findAll(),
        ];
        return view('admin/tables/index', $data);
    }

    public function store()
    {
        $rules = [
            'table_number' => 'required|min_length[1]|max_length[20]|is_unique[tables.table_number]',
            'name'         => 'required|min_length[1]|max_length[50]',
            'capacity'     => 'required|is_natural_no_zero',
            'status'       => 'required|in_list[available,occupied,reserved,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $tableNumber = $this->request->getPost('table_number');
        $tableName   = $this->request->getPost('name');

        $this->tableModel->save([
            'table_number' => $tableNumber,
            'name'         => $tableName,
            'capacity'     => $this->request->getPost('capacity'),
            'status'       => $this->request->getPost('status'),
            'is_active'    => (int) $this->request->getPost('is_active'),
        ]);

        // Catat ke Log Aktivitas
        log_activity('CREATE_TABLE', "Menambahkan meja baru: '{$tableNumber}' - {$tableName}");

        return redirect()->to('/admin/tables')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        $table = $this->tableModel->find($id);
        if (!$table) {
            return redirect()->to('/admin/tables')->with('error', 'Meja tidak ditemukan.');
        }

        $rules = [
            'table_number' => "required|min_length[1]|max_length[20]|is_unique[tables.table_number,id,{$id}]",
            'name'         => 'required|min_length[1]|max_length[50]',
            'capacity'     => 'required|is_natural_no_zero',
            'status'       => 'required|in_list[available,occupied,reserved,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $tableNumber = $this->request->getPost('table_number');
        $tableName   = $this->request->getPost('name');

        $this->tableModel->update($id, [
            'table_number' => $tableNumber,
            'name'         => $tableName,
            'capacity'     => $this->request->getPost('capacity'),
            'status'       => $this->request->getPost('status'),
            'is_active'    => (int) $this->request->getPost('is_active'),
        ]);

        // Catat ke Log Aktivitas
        log_activity('UPDATE_TABLE', "Mengubah data meja ID #{$id} ('{$tableNumber}' - {$tableName})");

        return redirect()->to('/admin/tables')->with('success', 'Meja berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $table = $this->tableModel->find($id);
        if ($table) {
            $this->tableModel->delete($id);

            // Catat ke Log Aktivitas
            log_activity('DELETE_TABLE', "Menghapus meja: '{$table['table_number']}' - {$table['name']} (ID #{$id})");

            return redirect()->to('/admin/tables')->with('success', 'Meja berhasil dihapus.');
        }

        return redirect()->to('/admin/tables')->with('error', 'Meja tidak ditemukan.');
    }
}