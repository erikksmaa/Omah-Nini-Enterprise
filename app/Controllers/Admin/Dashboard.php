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
        // $this->pelangganModel = new PelangganModel();
    }

    public function index()
    {
        // Ambil data stok menipis
        $stokMenipis = $this->produkModel->getLowStockProducts(10);
        
        // Proses nama produk di controller
        $stokMenipisWithName = [];
        foreach ($stokMenipis as $item) {
            $motif = $this->motifModel->find($item['id_motif'] ?? 0);
            $warna = $this->warnaModel->find($item['id_warna'] ?? 0);
            $nama_produk = ($motif['nama_motif'] ?? '?') . ' - ' . ($warna['nama_warna'] ?? '?');
            
            $stokMenipisWithName[] = [
                'id' => $item['id'],
                'nama_produk' => $nama_produk,
                'stok' => $item['stok'],
                'min_stok' => $item['min_stok']
            ];
        }
        
        $data = [
            'title' => 'Dashboard Admin',
            'username' => session()->get('username'),
            'role' => session()->get('role'),
            'total_produk' => $this->produkModel->countAllResults(),
            'total_supplier' => $this->supplierModel->countAllResults(),
            'total_motif' => $this->motifModel->countAllResults(),
            'total_warna' => $this->warnaModel->countAllResults(),
            'total_user' => $this->userModel->countAllResults(),
            // 'total_pelanggan' => $this->pelangganModel->countAllResults(),
            'stok_menipis' => $stokMenipisWithName,
            'transaksi_hari_ini' => $this->transaksiModel->getCountTransactionsToday(),
            'pembelian_bulan_ini' => $this->pembelianModel->getCountPembelianBulanIni(),
            'penjualan_bulan_ini' => $this->transaksiModel->getCountTransactionsThisMonth(),
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
}