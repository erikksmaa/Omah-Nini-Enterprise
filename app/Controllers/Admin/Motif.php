<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MotifModel;
use App\Models\SupplierModel;

class Motif extends BaseController
{
    protected $motifModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->motifModel = new MotifModel();
        $this->supplierModel = new SupplierModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $filter_supplier = $this->request->getGet('filter_supplier');
        $perPage = 10;
        
        $builder = $this->motifModel->select('motif.*, supplier.nama as nama_supplier')
                                    ->join('supplier', 'supplier.id = motif.id_supplier')
                                    ->orderBy('motif.id', 'DESC');
        
        // Filter berdasarkan keyword
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('motif.nama_motif', $keyword)
                ->orLike('supplier.nama', $keyword)
                ->groupEnd();
        }
        
        // Filter berdasarkan supplier
        if (!empty($filter_supplier)) {
            $builder->where('motif.id_supplier', $filter_supplier);
        }
        
        $motif = $builder->paginate($perPage);
        $pager = $this->motifModel->pager;
        
        $data = [
            'title' => 'Kelola Data Motif',
            'motif' => $motif,
            'pager' => $pager,
            'keyword' => $keyword,
            'filter_supplier' => $filter_supplier,
            'suppliers' => $this->supplierModel->getOptions(),
        ];
        
        return view('admin/motif/index', $data);
    }

    public function store()
    {
        // Validasi menggunakan rules dari model
        if (!$this->validate($this->motifModel->validationRules, $this->motifModel->validationMessages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $this->motifModel->save([
                'id_supplier' => $this->request->getPost('id_supplier'),
                'nama_motif' => $this->request->getPost('nama_motif'),
                'keterangan' => $this->request->getPost('keterangan'),
            ]);

            return redirect()->to('/admin/motif')
                ->with('success', 'Motif berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan motif: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        // Cek apakah motif ada
        $motif = $this->motifModel->find($id);
        if (!$motif) {
            return redirect()->back()
                ->with('error', 'Data motif tidak ditemukan.');
        }

        // Validasi
        $rules = $this->motifModel->validationRules;

        if (!$this->validate($rules, $this->motifModel->validationMessages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $this->motifModel->update($id, [
                'id_supplier' => $this->request->getPost('id_supplier'),
                'nama_motif' => $this->request->getPost('nama_motif'),
                'keterangan' => $this->request->getPost('keterangan'),
            ]);

            return redirect()->to('/admin/motif')
                ->with('success', 'Motif berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui motif: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $motif = $this->motifModel->find($id);
        if (!$motif) {
            return redirect()->back()
                ->with('error', 'Data motif tidak ditemukan.');
        }

        // Cek apakah motif memiliki produk terkait
        if ($this->motifModel->hasRelatedProducts($id)) {
            $productCount = $this->motifModel->getProductCount($id);
            return redirect()->back()
                ->with('error', "Motif tidak bisa dihapus karena masih digunakan pada {$productCount} produk.");
        }

        try {
            $this->motifModel->delete($id);
            return redirect()->to('/admin/motif')
                ->with('success', 'Motif berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus motif: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get motif by supplier ID untuk dropdown dinamis
     */
    public function getBySupplier($id_supplier)
    {
        $motif = $this->motifModel->where('id_supplier', $id_supplier)
            ->orderBy('nama_motif', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $motif
        ]);
    }
}