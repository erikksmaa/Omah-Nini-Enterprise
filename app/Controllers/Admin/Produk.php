<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\SupplierModel;
use App\Models\MotifModel;
use App\Models\WarnaModel;

class Produk extends BaseController
{
    protected $produkModel;
    protected $supplierModel;
    protected $motifModel;
    protected $warnaModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->supplierModel = new SupplierModel();
        $this->motifModel = new MotifModel();
        $this->warnaModel = new WarnaModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $perPage = 10;
        
        $builder = $this->produkModel->select('produk.*, supplier.nama as nama_supplier, motif.nama_motif, warna.nama_warna')
                                     ->join('supplier', 'supplier.id = produk.id_supplier')
                                     ->join('motif', 'motif.id = produk.id_motif')
                                     ->join('warna', 'warna.id = produk.id_warna')
                                     ->orderBy('produk.id', 'DESC');
        
        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('produk.sku', $keyword)
                    ->orLike('motif.nama_motif', $keyword)
                    ->orLike('warna.nama_warna', $keyword)
                    ->orLike('supplier.nama', $keyword)
                    ->groupEnd();
        }
        
        $produk = $builder->paginate($perPage);
        $pager = $this->produkModel->pager;
        
        $data = [
            'title'     => 'Kelola Data Produk',
            'produk'    => $produk,
            'pager'     => $pager,
            'keyword'   => $keyword,
            'suppliers' => $this->supplierModel->getOptions(),
            'motifs'    => $this->motifModel->getOptions(),
            'warnas'    => $this->warnaModel->getOptions()
        ];
        
        return view('admin/produk/index', $data);
    }

    public function create()
    {
        $data = [
            'title'     => 'Tambah Produk Baru',
            'suppliers' => $this->supplierModel->getOptions(),
            'motifs'    => $this->motifModel->getOptions(),
            'warnas'    => $this->warnaModel->getOptions()
        ];
        
        return view('admin/produk/create', $data);
    }

    public function store()
    {
        // Validasi
        if (!$this->validate($this->produkModel->validationRules, $this->produkModel->validationMessages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            // Generate SKU jika tidak diisi
            $sku = $this->request->getPost('sku');
            if (empty($sku)) {
                $sku = $this->produkModel->generateSku(
                    $this->request->getPost('id_supplier'),
                    $this->request->getPost('id_motif'),
                    $this->request->getPost('id_warna')
                );
            }
            
            $this->produkModel->save([
                'sku'         => $sku,
                'id_supplier' => $this->request->getPost('id_supplier'),
                'id_motif'    => $this->request->getPost('id_motif'),
                'id_warna'    => $this->request->getPost('id_warna'),
                'stok'        => $this->request->getPost('stok') ?? 0,
                'min_stok'    => $this->request->getPost('min_stok') ?? 0,
                'keterangan'  => $this->request->getPost('keterangan'),
            ]);

            return redirect()->to('/admin/produk')
                ->with('success', 'Produk berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $produk = $this->produkModel->getByIdWithRelations($id);
        if (!$produk) {
            return redirect()->to('/admin/produk')
                ->with('error', 'Produk tidak ditemukan.');
        }
        
        $data = [
            'title'     => 'Edit Produk',
            'produk'    => $produk,
            'suppliers' => $this->supplierModel->getOptions(),
            'motifs'    => $this->motifModel->getOptions(),
            'warnas'    => $this->warnaModel->getOptions()
        ];
        
        return view('admin/produk/edit', $data);
    }

    public function update($id)
    {
        // Cek apakah produk ada
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->back()
                ->with('error', 'Data produk tidak ditemukan.');
        }

        // Validasi dengan aturan yang disesuaikan untuk update
        $rules = $this->produkModel->validationRules;
        
        // Modify is_unique rule untuk update
        if (isset($rules['sku'])) {
            $rules['sku'] = str_replace(
                'is_unique[produk.sku]',
                'is_unique[produk.sku,id,' . $id . ']',
                $rules['sku']
            );
        }
        
        if (!$this->validate($rules, $this->produkModel->validationMessages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $this->produkModel->update($id, [
                'sku'         => $this->request->getPost('sku'),
                'id_supplier' => $this->request->getPost('id_supplier'),
                'id_motif'    => $this->request->getPost('id_motif'),
                'id_warna'    => $this->request->getPost('id_warna'),
                'stok'        => $this->request->getPost('stok') ?? 0,
                'min_stok'    => $this->request->getPost('min_stok') ?? 0,
                'keterangan'  => $this->request->getPost('keterangan'),
            ]);

            return redirect()->to('/admin/produk')
                ->with('success', 'Produk berhasil diperbarui.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->back()
                ->with('error', 'Data produk tidak ditemukan.');
        }

        // Cek apakah produk memiliki relasi di detail_pembelian atau detail_transaksi
        $detailPembelianModel = new \App\Models\DetailPembelianModel();
        $detailTransaksiModel = new \App\Models\DetailTransaksiModel();
        
        $pembelianCount = $detailPembelianModel->where('id_produk', $id)->countAllResults();
        $transaksiCount = $detailTransaksiModel->where('id_produk', $id)->countAllResults();
        
        if ($pembelianCount > 0 || $transaksiCount > 0) {
            return redirect()->back()
                ->with('error', "Produk tidak bisa dihapus karena sudah memiliki riwayat transaksi.");
        }

        try {
            $this->produkModel->delete($id);
            return redirect()->to('/admin/produk')
                ->with('success', 'Produk berhasil dihapus.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get motif by supplier
     */
    public function getMotifBySupplier()
    {
        $id_supplier = $this->request->getPost('id_supplier');
        $motifs = $this->motifModel->where('id_supplier', $id_supplier)->findAll();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $motifs
        ]);
    }

    /**
     * AJAX: Generate SKU
     */
    public function generateSku()
    {
        $id_supplier = $this->request->getPost('id_supplier');
        $id_motif = $this->request->getPost('id_motif');
        $id_warna = $this->request->getPost('id_warna');
        
        if (empty($id_supplier) || empty($id_motif) || empty($id_warna)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Supplier, motif, dan warna harus dipilih terlebih dahulu.'
            ]);
        }
        
        $sku = $this->produkModel->generateSku($id_supplier, $id_motif, $id_warna);
        
        return $this->response->setJSON([
            'status' => 'success',
            'sku' => $sku
        ]);
    }
}