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
        // Get ALL low stock products for accurate count
        $allStokMenipis = $this->produkModel->getLowStockProducts();
        $stokMenipisData = [];
        foreach ($allStokMenipis as $item) {
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

            // Statistik
            'total_produk' => $this->produkModel->getTotalProduk(),
            'total_supplier' => $this->supplierModel->getTotalSupplier(),
            'total_motif' => $this->motifModel->getTotalMotif(),
            'total_warna' => $this->warnaModel->getTotalWarna(),
            'total_user' => $this->userModel->getTotalUser(),
            'total_pelanggan' => $this->pelangganModel->getTotalPelanggan(),
            'total_stok' => $this->produkModel->getTotalStockQuantity(),

            // Stok — show all for count, but view limits display to 5
            'stok_menipis' => $stokMenipisData,
            'total_stok_menipis' => count($stokMenipisData),
            'stok_habis' => count($this->produkModel->getOutOfStockProducts()),

            // Transaksi
            'transaksi_hari_ini' => $this->transaksiModel->getCountTransactionsToday(),
            'pembelian_hari_ini' => $this->pembelianModel->getCountPembelianHariIni(),
            'transaksi_bulan_ini' => $this->transaksiModel->getCountTransactionsThisMonth(),
            'pembelian_bulan_ini' => $this->pembelianModel->getCountPembelianBulanIni(),

            // Top Produk & Aktivitas (dari model Transaksi)
            'top_produk' => $this->transaksiModel->getTopProducts(5),
            'aktivitas_terbaru' => $this->transaksiModel->getRecentActivities(5),
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

    /**
     * API: Weekly activity (pembelian + penjualan) for grouped bar chart
     */
    public function getWeeklyActivity()
    {
        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dateLabel = date('d/m', strtotime($date));

            $penjualan = $this->transaksiModel->where('DATE(tanggal_transaksi)', $date)->countAllResults(false);
            $pembelian = $this->pembelianModel->where('DATE(tanggal_pembelian)', $date)->countAllResults(false);

            $result[] = [
                'date' => $dateLabel,
                'penjualan' => $penjualan,
                'pembelian' => $pembelian,
            ];
        }

        return $this->response->setJSON($result);
    }

    /**
     * API: Monthly activity (12 months) for grouped bar chart
     */
    public function getMonthlyActivity()
    {
        $result = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd = date('Y-m-t', strtotime("-$i months"));
            $monthLabel = date('M Y', strtotime($monthStart));

            $penjualan = $this->transaksiModel
                ->where('DATE(tanggal_transaksi) >=', $monthStart)
                ->where('DATE(tanggal_transaksi) <=', $monthEnd)
                ->countAllResults(false);

            $pembelian = $this->pembelianModel
                ->where('DATE(tanggal_pembelian) >=', $monthStart)
                ->where('DATE(tanggal_pembelian) <=', $monthEnd)
                ->countAllResults(false);

            $result[] = [
                'date' => $monthLabel,
                'penjualan' => $penjualan,
                'pembelian' => $pembelian,
            ];
        }

        return $this->response->setJSON($result);
    }
}