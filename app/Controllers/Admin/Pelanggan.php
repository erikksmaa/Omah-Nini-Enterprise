<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PelangganModel;

class Pelanggan extends BaseController
{
    protected $pelangganModel;

    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $allowedPerPage = [10, 25, 50, 100];
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }
        
        $builder = $this->pelangganModel->orderBy('id', 'DESC');
        
        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('nama', $keyword)
                    ->orLike('no_telp', $keyword)
                    ->groupEnd();
        }
        
        $pelanggan = $builder->paginate($perPage);
        $pager = $this->pelangganModel->pager;
        
        $data = [
            'title'     => 'Kelola Data Pelanggan',
            'pelanggan' => $pelanggan,
            'pager'     => $pager,
            'keyword'   => $keyword
        ];
        
        return view('admin/pelanggan/index', $data);
    }

    public function store()
    {
        // Validasi menggunakan rules dari model
        if (!$this->validate($this->pelangganModel->validationRules, $this->pelangganModel->validationMessages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $this->pelangganModel->save([
                'nama'    => $this->request->getPost('nama'),
                'alamat'  => $this->request->getPost('alamat'),
                'no_telp' => $this->request->getPost('no_telp'),
            ]);

            return redirect()->to('/admin/pelanggan')
                ->with('success', 'Pelanggan berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan pelanggan: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        // Cek apakah pelanggan ada
        $pelanggan = $this->pelangganModel->getById($id);
        if (!$pelanggan) {
            return redirect()->back()
                ->with('error', 'Data pelanggan tidak ditemukan.');
        }

        // Validasi
        if (!$this->validate($this->pelangganModel->validationRules, $this->pelangganModel->validationMessages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $this->pelangganModel->update($id, [
                'nama'    => $this->request->getPost('nama'),
                'alamat'  => $this->request->getPost('alamat'),
                'no_telp' => $this->request->getPost('no_telp'),
            ]);

            return redirect()->to('/admin/pelanggan')
                ->with('success', 'Pelanggan berhasil diperbarui.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pelanggan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $pelanggan = $this->pelangganModel->getById($id);
        if (!$pelanggan) {
            return redirect()->back()
                ->with('error', 'Data pelanggan tidak ditemukan.');
        }

        // Cek apakah pelanggan memiliki transaksi terkait
        $transaksiModel = new \App\Models\TransaksiModel();
        $transaksiCount = $transaksiModel->where('id_pelanggan', $id)->countAllResults();
        
        if ($transaksiCount > 0) {
            return redirect()->back()
                ->with('error', "Pelanggan tidak bisa dihapus karena memiliki {$transaksiCount} riwayat transaksi.");
        }

        try {
            $this->pelangganModel->delete($id);
            return redirect()->to('/admin/pelanggan')
                ->with('success', 'Pelanggan berhasil dihapus.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus pelanggan: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get pelanggan by ID for edit modal
     */
    public function getData($id)
    {
        $pelanggan = $this->pelangganModel->getById($id);
        if ($pelanggan) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $pelanggan
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Data tidak ditemukan'
        ]);
    }

    /**
     * AJAX: Search pelanggan for dropdown
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');
        $pelanggan = $this->pelangganModel->search($keyword, 10);
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $pelanggan
        ]);
    }
}