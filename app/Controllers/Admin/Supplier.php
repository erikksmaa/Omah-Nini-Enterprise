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
        $keyword = $this->request->getGet('keyword');
        $perPage = 10;
        
        $builder = $this->supplierModel->orderBy('id', 'DESC');
        
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('nama', $keyword)
                ->orLike('kontak', $keyword)
                ->orLike('email', $keyword)
                ->groupEnd();
        }
        
        $supplier = $builder->paginate($perPage);
        $pager = $this->supplierModel->pager;
        
        $data = [
            'title' => 'Kelola Data Supplier',
            'supplier' => $supplier,
            'pager' => $pager,
            'keyword' => $keyword
        ];
        
        return view('admin/supplier/index', $data);
    }

    public function store()
    {
        // Validasi menggunakan rules dari model
        if (!$this->validate($this->supplierModel->validationRules, $this->supplierModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $this->supplierModel->save([
                'nama'   => $this->request->getPost('nama'),
                'kontak' => $this->request->getPost('kontak'),
                'email'  => $this->request->getPost('email'),
                'alamat' => $this->request->getPost('alamat'),
            ]);

            return redirect()->to('/admin/supplier')->with('success', 'Supplier berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan supplier: ' . $e->getMessage());
        }
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

        $supplier = $this->supplierModel->getById($id);
        if (!$supplier) {
            return redirect()->back()->with('error', 'Data supplier tidak ditemukan.');
        }

        try {
            $this->supplierModel->update($id, [
                'nama'   => $this->request->getPost('nama'),
                'kontak' => $this->request->getPost('kontak'),
                'email'  => $this->request->getPost('email'),
                'alamat' => $this->request->getPost('alamat'),
            ]);

            return redirect()->to('/admin/supplier')->with('success', 'Supplier berhasil diperbarui.');
            
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui supplier: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $supplier = $this->supplierModel->getById($id);
        if (!$supplier) {
            return redirect()->back()->with('error', 'Data supplier tidak ditemukan.');
        }

        // Cek apakah supplier memiliki produk terkait
        if ($this->supplierModel->hasRelatedProducts($id)) {
            $productCount = $this->supplierModel->getProductCount($id);
            return redirect()->back()->with('error', "Supplier tidak bisa dihapus karena masih memasok {$productCount} produk.");
        }

        try {
            $this->supplierModel->delete($id);
            return redirect()->to('/admin/supplier')->with('success', 'Supplier berhasil dihapus.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus supplier: ' . $e->getMessage());
        }
    }
}