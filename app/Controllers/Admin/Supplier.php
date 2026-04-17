<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SupplierModel;

class Supplier extends BaseController
{
    protected $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
    }

   public function index()
    {
        // Gunakan paginate(10) untuk 10 data per halaman
        $supplier = $this->supplierModel->orderBy('id', 'DESC')->paginate(10);
        
        $data = [
            'title' => 'Kelola Data Supplier',
            'supplier' => $supplier,
            'pager' => $this->supplierModel->pager
        ];
        
        return view('admin/supplier/index', $data);
    }

    public function store()
    {
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'kontak' => 'required|min_length[10]|max_length[15]',
            'email' => 'permit_empty|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->supplierModel->save([
            'nama'   => $this->request->getPost('nama'),
            'kontak' => $this->request->getPost('kontak'),
            'email'  => $this->request->getPost('email'),
            'alamat' => $this->request->getPost('alamat'),
        ]);

        return redirect()->to('/admin/supplier')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update($id)
    {
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'kontak' => 'required|min_length[10]|max_length[15]',
            'email' => 'permit_empty|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->supplierModel->update($id, [
            'nama'   => $this->request->getPost('nama'),
            'kontak' => $this->request->getPost('kontak'),
            'email'  => $this->request->getPost('email'),
            'alamat' => $this->request->getPost('alamat'),
        ]);

        return redirect()->to('/admin/supplier')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->supplierModel->delete($id);
        return redirect()->to('/admin/supplier')->with('success', 'Supplier berhasil dihapus.');
    }
}