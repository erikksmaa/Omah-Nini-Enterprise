<?php

namespace App\Controllers\Pemilik;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\SupplierModel;
use App\Models\MotifModel;
use App\Models\WarnaModel;
use App\Models\UserModel;
use App\Models\PelangganModel;
use App\Models\TransaksiModel;
use App\Models\PembelianModel;

class Dashboard extends BaseController
{
    protected $produkModel;
    protected $supplierModel;
    protected $motifModel;
    protected $warnaModel;
    protected $userModel;
    protected $pelangganModel;
    protected $transaksiModel;
    protected $pembelianModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->supplierModel = new SupplierModel();
        $this->motifModel = new MotifModel();
        $this->warnaModel = new WarnaModel();
        $this->userModel = new UserModel();
        $this->pelangganModel = new PelangganModel();
        $this->transaksiModel = new TransaksiModel();
        $this->pembelianModel = new PembelianModel();
    }

    public function index()
    {
        // Format stok menipis
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
            'title' => 'Dashboard Owner',

            // Statistik Utama
            'total_produk' => $this->produkModel->getTotalProduk(),
            'total_supplier' => $this->supplierModel->getTotalSupplier(),
            'total_motif' => $this->motifModel->getTotalMotif(),
            'total_warna' => $this->warnaModel->getTotalWarna(),
            'total_user' => $this->userModel->getTotalUser(),
            'total_pelanggan' => $this->pelangganModel->getTotalPelanggan(),

            // Statistik Stok
            'total_stok' => $this->produkModel->getTotalStockQuantity(),
            'stok_menipis' => $stokMenipisData,
            'stok_habis' => count($this->produkModel->getOutOfStockProducts()),

            // Statistik Transaksi
            'transaksi_hari_ini' => $this->transaksiModel->getCountTransactionsToday(),
            'transaksi_bulan_ini' => $this->transaksiModel->getCountTransactionsThisMonth(),
            'pembelian_bulan_ini' => $this->pembelianModel->getCountPembelianBulanIni(),

            // Tren (perbandingan bulan lalu)
            'transaksi_bulan_lalu' => $this->transaksiModel->getCountTransactionsLastMonth(),
        ];

        return view('pemilik/dashboard/index', $data);
    }

    /**
     * API untuk grafik aktivitas 7 hari terakhir
     */
    public function getWeeklyActivity()
    {
        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dateLabel = date('d/m', strtotime($date));

            // Hitung penjualan (barang keluar) per hari
            $penjualan = $this->transaksiModel->where('DATE(tanggal_transaksi)', $date)->countAllResults();

            // Hitung pembelian (barang masuk) per hari
            $pembelian = $this->pembelianModel->where('DATE(tanggal_pembelian)', $date)->countAllResults();

            $result[] = [
                'date' => $dateLabel,
                'penjualan' => $penjualan,
                'pembelian' => $pembelian
            ];
        }

        return $this->response->setJSON($result);
    }
}