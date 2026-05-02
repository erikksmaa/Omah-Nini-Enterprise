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

    /**
     * Halaman utama manajemen stok
     * Menampilkan semua produk dengan filter supplier dan status stok
     */
    public function index()
    {
        // Data filter
        $supplierId = $this->request->getGet('supplier');
        $status     = $this->request->getGet('status'); // menipis, habis, aman, semua

        // Build query dasar
        $builder = $this->produkModel
            ->select('produk.*, supplier.nama as nama_supplier, motif.nama_motif, warna.nama_warna')
            ->join('supplier', 'supplier.id = produk.id_supplier')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna');

        // Filter supplier
        if (!empty($supplierId)) {
            $builder->where('produk.id_supplier', $supplierId);
        }

        // Filter status
        if ($status === 'menipis') {
            $builder->where('produk.stok <= produk.min_stok')
                    ->where('produk.stok >', 0);
        } elseif ($status === 'habis') {
            $builder->where('produk.stok', 0);
        } elseif ($status === 'aman') {
            $builder->where('produk.stok > produk.min_stok');
        }
        // Jika 'semua' atau kosong, tampilkan semua

        $produk = $builder->orderBy('produk.stok', 'ASC')
                          ->paginate(15);

        // Data untuk view
        $data = [
            'title'           => 'Manajemen Stok',
            'produk'          => $produk,
            'pager'           => $this->produkModel->pager,
            'suppliers'       => $this->supplierModel->findAll(),
            'selectedSupplier'=> $supplierId,
            'selectedStatus'  => $status,
            'total_produk'    => $this->produkModel->countAllResults(),
            'total_stok'      => $this->produkModel->getTotalStockQuantity(),
        ];

        return view('karyawan/stok/index', $data);
    }

    /**
     * Detail stok satu produk, termasuk riwayat perubahannya
     */
    public function detail($id)
    {
        $produk = $this->produkModel->getByIdWithRelations($id);

        if (!$produk) {
            return redirect()->to('/karyawan/stok')->with('error', 'Produk tidak ditemukan.');
        }

        // Ambil log stok khusus produk ini
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

    /**
     * Form opname (penyesuaian stok)
     */
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

    /**
     * Proses update stok opname
     */
    public function updateOpname($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->to('/karyawan/stok')->with('error', 'Produk tidak ditemukan.');
        }

        $rules = [
            'stok_baru' => 'required|integer|greater_than_equal_to[0]',
            'keterangan'=> 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $stokBaru    = $this->request->getPost('stok_baru');
        $keterangan  = $this->request->getPost('keterangan') ?? 'Penyesuaian stok (opname)';
        $stokLama    = $produk['stok'];
        $perubahan   = $stokBaru - $stokLama;
        $userId      = session()->get('user_id');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update stok produk
            $this->produkModel->update($id, ['stok' => $stokBaru]);

            // Catat di log stok
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

    /**
     * Riwayat perubahan stok (semua produk)
     */
    public function history()
    {
        $produkId  = $this->request->getGet('produk');
        $tipeRef   = $this->request->getGet('tipe'); // pembelian, penjualan, penyesuaian

        $builder = $this->logStokModel
            ->select('log_stok.*, produk.sku, motif.nama_motif, warna.nama_warna, users.username')
            ->join('produk', 'produk.id = log_stok.id_produk')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->join('users', 'users.user_id = log_stok.id_user');

        if (!empty($produkId)) {
            $builder->where('log_stok.id_produk', $produkId);
        }

        if (!empty($tipeRef)) {
            $builder->where('log_stok.tipe_ref', $tipeRef);
        }

        $logStok = $builder->orderBy('log_stok.id', 'DESC')
                           ->paginate(15);

        // Ambil daftar produk untuk filter dropdown
        $produkList = $this->produkModel->getAllForDropdown();

        $data = [
            'title'           => 'Riwayat Perubahan Stok',
            'log_stok'        => $logStok,
            'pager'           => $this->logStokModel->pager,
            'produk_list'     => $produkList,
            'selectedProduk'  => $produkId,
            'selectedTipe'    => $tipeRef,
        ];

        return view('karyawan/stok/history', $data);
    }
}