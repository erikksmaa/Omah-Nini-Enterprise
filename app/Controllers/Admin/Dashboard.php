<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\SupplierModel;
use App\Models\MotifModel;
use App\Models\WarnaModel;
use App\Models\UserModel;
use App\Models\TransaksiModel;
use App\Models\PembelianModel;
use App\Models\PelangganModel;

class Dashboard extends BaseController
{
    protected $produkModel;
    protected $supplierModel;
    protected $motifModel;
    protected $warnaModel;
    protected $userModel;
    protected $transaksiModel;
    protected $pembelianModel;
    protected $pelangganModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->supplierModel = new SupplierModel();
        $this->motifModel = new MotifModel();
        $this->warnaModel = new WarnaModel();
        $this->userModel = new UserModel();
        $this->transaksiModel = new TransaksiModel();
        $this->pembelianModel = new PembelianModel();
        $this->pelangganModel = new PelangganModel();
    }

    public function index()
    {
        $stokMenipis = $this->produkModel->getLowStockProducts(10);
        $stokMenipisData = [];
        foreach ($stokMenipis as $item) {
            $stokMenipisData[] = [
                'id' => $item['id'],
                'nama_produk' => ($item['nama_motif'] ?? '?') . ' - ' . ($item['nama_warna'] ?? '?'),
                'sku' => $item['sku'],
                'stok' => $item['stok'],
                'min_stok' => $item['min_stok'],
            ];
        }

        

        $data = [
            'title' => 'Dashboard Admin',

            // Statistik Utama
            'total_produk' => $this->produkModel->getTotalProduk(),
            'total_supplier' => $this->supplierModel->getTotalSupplier(),
            'total_motif' => $this->motifModel->getTotalMotif(),
            'total_warna' => $this->warnaModel->getTotalWarna(),
            'total_user' => $this->userModel->getTotalUser(),
            'total_pelanggan' => $this->pelangganModel->getTotalPelanggan(),

            // ========== TAMBAHKAN INI ==========
            'total_stok' => $this->produkModel->getTotalStockQuantity(),  // ← BARIS INI

            // Stok
            'stok_menipis' => $stokMenipisData,
            'stok_habis' => count($this->produkModel->getOutOfStockProducts()),

            // Transaksi
            'transaksi_hari_ini' => $this->transaksiModel->getCountTransactionsToday(),
            'pembelian_hari_ini' => $this->pembelianModel->getCountPembelianHariIni(),
            'transaksi_bulan_ini' => $this->transaksiModel->getCountTransactionsThisMonth(),
            'pembelian_bulan_ini' => $this->pembelianModel->getCountPembelianBulanIni(),
        ];

        return view('admin/dashboard/index', $data);
    }

    // API Methods
    public function getLowStockData()
    {
        $lowStock = $this->produkModel->getLowStockProducts(5);

        $data = [];
        foreach ($lowStock as $item) {
            $motif = $this->motifModel->find($item['id_motif'] ?? 0);
            $warna = $this->warnaModel->find($item['id_warna'] ?? 0);
            $nama_produk = ($motif['nama_motif'] ?? '?') . ' - ' . ($warna['nama_warna'] ?? '?');

            $data[] = [
                'id' => $item['id'],
                'nama_produk' => $nama_produk,
                'stok' => $item['stok'],
                'min_stok' => $item['min_stok']
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getDashboardStats()
    {
        $weeklyTransaksi = $this->transaksiModel->getWeeklyCount();
        $totalStokQuantity = $this->produkModel->getTotalStockQuantity();

        return $this->response->setJSON([
            'status' => 'success',
            'weekly_transaksi' => $weeklyTransaksi,
            'total_stok_quantity' => $totalStokQuantity
        ]);
    }

    public function getWeeklySales()
{
    $data = $this->transaksiModel->getWeeklyCount();
    
    // Pastikan $data adalah array
    if (!$data || !is_array($data)) {
        $data = [];
    }
    
    return $this->response->setJSON($data);
}
}