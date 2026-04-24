<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriModel;

class Kategori extends BaseController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $perPage = 10;

        $builder = $this->kategoriModel->orderBy('id', 'DESC');

        if (!empty($keyword)) {
            $builder->like('nama', $keyword);
        }

        $kategori = $builder->paginate($perPage);
        $pager = $this->kategoriModel->pager;

        // Debug: cek data
        log_message('debug', 'Kategori count: ' . count($kategori));
        log_message('debug', 'Total data: ' . $pager->getTotal());

        $data = [
            'title' => 'Kelola Kategori Barang',
            'kategori' => $kategori,
            'pager' => $pager,
            'keyword' => $keyword
        ];

        return view('admin/kategori/index', $data);
    }

    public function store()
    {
        // Validasi menggunakan rules dari model
        if (!$this->validate($this->kategoriModel->validationRules, $this->kategoriModel->validationMessages)) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        try {
            $this->kategoriModel->save([
                'nama' => $this->request->getPost('nama'),
                'deskripsi' => $this->request->getPost('deskripsi')
            ]);
            return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        // Update validation rules (skip unique check for current ID)
        $rules = [
            'nama' => "required|min_length[3]|max_length[100]|is_unique[kategori.nama,id,{$id}]",
            'deskripsi' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        $kategoriLama = $this->kategoriModel->getById($id);
        if (!$kategoriLama) {
            return redirect()->back()->with('error', 'Data kategori tidak ditemukan.');
        }

        try {
            $this->kategoriModel->update($id, [
                'nama' => $this->request->getPost('nama'),
                'deskripsi' => $this->request->getPost('deskripsi')
            ]);
            return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if ($this->kategoriModel->hasRelatedProducts($id)) {
            $productCount = $this->kategoriModel->getProductCount($id);
            return redirect()->back()->with('error', "Kategori tidak bisa dihapus karena masih digunakan oleh {$productCount} produk.");
        }

        try {
            $this->kategoriModel->delete($id);
            return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}