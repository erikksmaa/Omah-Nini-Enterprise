<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WarnaModel;

class Warna extends BaseController
{
    protected $warnaModel;

    public function __construct()
    {
        $this->warnaModel = new WarnaModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $perPage = 10;
        
        $builder = $this->warnaModel->orderBy('nama_warna', 'ASC');
        
        if (!empty($keyword)) {
            $builder->like('nama_warna', $keyword);
        }
        
        $warna = $builder->paginate($perPage);
        $pager = $this->warnaModel->pager;
        
        $data = [
            'title' => 'Kelola Data Warna',
            'warna' => $warna,
            'pager' => $pager,
            'keyword' => $keyword
        ];
        
        return view('admin/warna/index', $data);
    }

    public function store()
    {
        // Validasi menggunakan rules dari model
        if (!$this->validate($this->warnaModel->validationRules, $this->warnaModel->validationMessages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $this->warnaModel->save([
                'nama_warna' => $this->request->getPost('nama_warna'),
                'kode_hex'   => $this->request->getPost('kode_hex'),
            ]);

            return redirect()->to('/admin/warna')
                ->with('success', 'Warna berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan warna: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        // Cek apakah warna ada
        $warna = $this->warnaModel->find($id);
        if (!$warna) {
            return redirect()->back()
                ->with('error', 'Data warna tidak ditemukan.');
        }

        // Validasi dengan aturan yang sudah disesuaikan untuk update
        $rules = $this->warnaModel->validationRules;
        
        // Modify is_unique rule untuk update (tambahkan pengecualian ID)
        if (isset($rules['nama_warna'])) {
            $rules['nama_warna'] = str_replace(
                'is_unique[warna.nama_warna]', 
                'is_unique[warna.nama_warna,id,' . $id . ']', 
                $rules['nama_warna']
            );
        }
        
        if (!$this->validate($rules, $this->warnaModel->validationMessages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $this->warnaModel->update($id, [
                'nama_warna' => $this->request->getPost('nama_warna'),
                'kode_hex'   => $this->request->getPost('kode_hex'),
            ]);

            return redirect()->to('/admin/warna')
                ->with('success', 'Warna berhasil diperbarui.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui warna: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $warna = $this->warnaModel->find($id);
        if (!$warna) {
            return redirect()->back()
                ->with('error', 'Data warna tidak ditemukan.');
        }

        // Cek apakah warna memiliki produk terkait
        if ($this->warnaModel->isUsed($id)) {
            $productCount = $this->warnaModel->getProductCount($id);
            return redirect()->back()
                ->with('error', "Warna tidak bisa dihapus karena masih digunakan pada {$productCount} produk.");
        }

        try {
            $this->warnaModel->delete($id);
            return redirect()->to('/admin/warna')
                ->with('success', 'Warna berhasil dihapus.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus warna: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get all warna for dropdown
     */
    public function getAll()
    {
        $warna = $this->warnaModel->getOptions();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $warna
        ]);
    }
}