<?php

namespace App\Controllers\Karyawan;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\LogStokModel;
use App\Models\SupplierModel;
use App\Models\MotifModel;
use App\Models\WarnaModel;

class Stok extends BaseController
{
    protected $produkModel;
    protected $logStokModel;
    protected $supplierModel;
    protected $motifModel;
    protected $warnaModel;

    public function __construct()
    {
        $this->produkModel   = new ProdukModel();
        $this->logStokModel  = new LogStokModel();
        $this->supplierModel = new SupplierModel();
        $this->motifModel    = new MotifModel();
        $this->warnaModel    = new WarnaModel();
    }

    // =========================================================
    //  INDEX — Tree nested (Brand → Motif → Produk)
    //  Filter & search ditangani JS di sisi client
    // =========================================================
    public function index()
    {
        $filter = $this->request->getGet('filter'); // 'menipis'
        
        $builder = $this->produkModel
            ->select('produk.*, supplier.nama as nama_supplier, supplier.id as supplier_id,
                      motif.nama_motif, motif.id as motif_id,
                      warna.nama_warna, warna.id as warna_id')
            ->join('supplier', 'supplier.id = produk.id_supplier')
            ->join('motif',    'motif.id    = produk.id_motif')
            ->join('warna',    'warna.id    = produk.id_warna')
            ->orderBy('supplier.nama',    'ASC')
            ->orderBy('motif.nama_motif', 'ASC')
            ->orderBy('warna.nama_warna', 'ASC');

        if ($filter == 'menipis') {
            $builder->where('produk.stok <= produk.min_stok')->where('produk.stok >', 0);
        }

        // Pagination setup - paginate products, then group into tree
        $allProducts = $builder->findAll();

        // ── Bangun struktur tree ────────────────────────────
        $tree = [];
        foreach ($allProducts as $p) {
            $supId   = $p['supplier_id'];
            $motifId = $p['motif_id'];

            if (!isset($tree[$supId])) {
                $tree[$supId] = [
                    'id'         => $supId,
                    'nama'       => $p['nama_supplier'],
                    'motif'      => [],
                    'total_stok' => 0,
                ];
            }

            if (!isset($tree[$supId]['motif'][$motifId])) {
                $tree[$supId]['motif'][$motifId] = [
                    'id'         => $motifId,
                    'nama'       => $p['nama_motif'],
                    'produk'     => [],
                    'total_stok' => 0,
                ];
            }

            $tree[$supId]['motif'][$motifId]['produk'][]         = $p;
            $tree[$supId]['total_stok']                          += (int) $p['stok'];
            $tree[$supId]['motif'][$motifId]['total_stok']       += (int) $p['stok'];
        }

        $data = [
            'title' => 'Kelola Stok',
            'tree'  => $tree,
            'filter'=> $filter
        ];

        return view('karyawan/stok/index', $data);
    }

    // =========================================================
    //  Selebihnya tidak berubah
    // =========================================================

    public function detail($id)
    {
        $produk = $this->produkModel->getByIdWithRelations($id);

        if (!$produk) {
            return redirect()->to('/karyawan/stok')->with('error', 'Produk tidak ditemukan.');
        }

        $logStok = $this->logStokModel
            ->select('log_stok.*, users.username')
            ->join('users', 'users.user_id = log_stok.id_user')
            ->where('log_stok.id_produk', $id)
            ->orderBy('log_stok.id', 'DESC')
            ->paginate(10, 'log');

        $data = [
            'title'    => 'Detail Stok Produk',
            'produk'   => $produk,
            'log_stok' => $logStok,
            'pager'    => $this->logStokModel->pager,
        ];

        return view('karyawan/stok/detail', $data);
    }

    public function opname($id)
    {
        $produk = $this->produkModel->getByIdWithRelations($id);

        if (!$produk) {
            return redirect()->to('/karyawan/stok')->with('error', 'Produk tidak ditemukan.');
        }

        $data = [
            'title'  => 'Opname Stok',
            'produk' => $produk,
        ];

        return view('karyawan/stok/opname', $data);
    }

    public function updateOpname($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->to('/karyawan/stok')->with('error', 'Produk tidak ditemukan.');
        }

        $rules = [
            'stok_baru'  => 'required|integer|greater_than_equal_to[0]',
            'keterangan' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $stokBaru  = $this->request->getPost('stok_baru');
        $stokLama  = $produk['stok'];
        $perubahan = $stokBaru - $stokLama;
        $userId    = session()->get('user_id');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->produkModel->update($id, ['stok' => $stokBaru]);

            $this->logStokModel->insert([
                'id_produk'        => $id,
                'id_user'          => $userId,
                'tipe_ref'         => 'penyesuaian',
                'id_ref'           => null,
                'jumlah_sebelum'   => $stokLama,
                'jumlah_perubahan' => $perubahan,
                'jumlah_sesudah'   => $stokBaru,
                'created_at'       => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan opname.');
            }

            return redirect()->to('/karyawan/stok/detail/' . $id)
                ->with('success', "Stok berhasil disesuaikan. Perubahan: {$perubahan} (dari {$stokLama} menjadi {$stokBaru}).");
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function history()
    {
        $produkId = $this->request->getGet('produk');
        $tipeRef  = $this->request->getGet('tipe');

        $builder = $this->logStokModel
            ->select('log_stok.*, produk.sku, motif.nama_motif, warna.nama_warna, users.username')
            ->join('produk', 'produk.id = log_stok.id_produk')
            ->join('motif',  'motif.id  = produk.id_motif')
            ->join('warna',  'warna.id  = produk.id_warna')
            ->join('users',  'users.user_id = log_stok.id_user');

        if (!empty($produkId)) {
            $builder->where('log_stok.id_produk', $produkId);
        }

        if (!empty($tipeRef)) {
            $builder->where('log_stok.tipe_ref', $tipeRef);
        }

        $logStok    = $builder->orderBy('log_stok.id', 'DESC')->paginate(15);
        $produkList = $this->produkModel->getAllForDropdown();

        $data = [
            'title'          => 'Riwayat Perubahan Stok',
            'log_stok'       => $logStok,
            'pager'          => $this->logStokModel->pager,
            'produk_list'    => $produkList,
            'selectedProduk' => $produkId,
            'selectedTipe'   => $tipeRef,
        ];

        return view('karyawan/stok/history', $data);
    }
}