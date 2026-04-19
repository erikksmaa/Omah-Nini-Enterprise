<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use App\Models\TransaksiModel;
use App\Models\PembelianModel;
use App\Models\KeuanganModel;
use App\Models\DetailTransaksiModel;

class Dashboard extends BaseController
{
    protected $produkModel;
    protected $kategoriModel;
    protected $supplierModel;
    protected $userModel;
    protected $transaksiModel;
    protected $pembelianModel;
    protected $keuanganModel;
    protected $detailTransaksiModel;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }

        $this->produkModel = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        $this->supplierModel = new SupplierModel();
        $this->userModel = new UserModel();
        $this->transaksiModel = new TransaksiModel();
        $this->pembelianModel = new PembelianModel();
        $this->keuanganModel = new KeuanganModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard Admin',
            'username' => session()->get('username'),
            'role' => session()->get('role'),
            'total_produk' => $this->produkModel->countAll(),
            'total_kategori' => $this->kategoriModel->countAll(),
            'total_supplier' => $this->supplierModel->countAll(),
            'total_user' => $this->userModel->countAll(),
            'stok_menipis' => $this->produkModel->where('stok <=', 'min_stok', false)->countAllResults(),
            'stok_habis' => $this->produkModel->where('stok', 0)->countAllResults(),
        ];

        return view('admin/dashboard', $data);
    }

    // API untuk Chart Penjualan 7 Hari Terakhir
    public function getWeeklySalesChart()
    {
        $db = \Config\Database::connect();
        
        $result = $db->table('transaksi')
            ->select('DATE(created_at) as tanggal, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
            ->groupBy('DATE(created_at)')
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();
        
        $labels = [];
        $values = [];
        
        // Buat template 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('d/m', strtotime($date));
            $found = false;
            foreach ($result as $row) {
                if ($row['tanggal'] == $date) {
                    $values[] = (float)$row['total'];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $values[] = 0;
            }
        }
        
        return $this->response->setJSON([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    // API untuk Chart Penjualan 12 Bulan Terakhir
    public function getMonthlySalesChart()
    {
        $db = \Config\Database::connect();
        
        $bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $values = array_fill(0, 12, 0);
        
        $result = $db->table('transaksi')
            ->select('MONTH(created_at) as bulan, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('YEAR(created_at)', date('Y'))
            ->groupBy('MONTH(created_at)')
            ->get()
            ->getResultArray();
        
        foreach ($result as $row) {
            $values[$row['bulan'] - 1] = (float)$row['total'];
        }
        
        return $this->response->setJSON([
            'labels' => $bulanNames,
            'values' => $values
        ]);
    }

    // API untuk Chart Laba/Rugi Bulan Ini
    public function getProfitLossChart()
    {
        $db = \Config\Database::connect();
        
        $pemasukan = $db->table('keuangan')
            ->selectSum('jumlah')
            ->where('tipe', 'pemasukan')
            ->where('MONTH(tanggal_transaksi)', date('m'))
            ->where('YEAR(tanggal_transaksi)', date('Y'))
            ->get()
            ->getRow()
            ->jumlah ?? 0;
        
        $pengeluaran = $db->table('keuangan')
            ->selectSum('jumlah')
            ->where('tipe', 'pengeluaran')
            ->where('MONTH(tanggal_transaksi)', date('m'))
            ->where('YEAR(tanggal_transaksi)', date('Y'))
            ->get()
            ->getRow()
            ->jumlah ?? 0;
        
        return $this->response->setJSON([
            'pemasukan' => (float)$pemasukan,
            'pengeluaran' => (float)$pengeluaran,
            'laba' => (float)($pemasukan - $pengeluaran)
        ]);
    }

    // API untuk Data Stok Menipis
    public function getLowStockData()
    {
        $produk = $this->produkModel
            ->select('produk.id, produk.nama_barang, produk.sku, produk.stok, produk.min_stok, kategori.nama as kategori')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->where('produk.stok <=', 'produk.min_stok', false)
            ->orderBy('produk.stok', 'ASC')
            ->limit(10)
            ->findAll();
        
        return $this->response->setJSON($produk);
    }

    // API untuk Produk Terlaris
    public function getTopProductsChart()
    {
        $db = \Config\Database::connect();
        
        $produk = $db->table('detail_transaksi')
            ->select('detail_transaksi.nama_produk, SUM(detail_transaksi.jumlah) as total_terjual')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('MONTH(transaksi.created_at)', date('m'))
            ->where('YEAR(transaksi.created_at)', date('Y'))
            ->groupBy('detail_transaksi.nama_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
        
        $labels = array_column($produk, 'nama_produk');
        $values = array_column($produk, 'total_terjual');
        
        return $this->response->setJSON([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    // API untuk Tipe Pembayaran
    public function getPaymentMethodChart()
    {
        $db = \Config\Database::connect();
        
        $result = $db->table('transaksi')
            ->select('tipe_pembayaran, COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->groupBy('tipe_pembayaran')
            ->get()
            ->getResultArray();
        
        $labels = [];
        $values = [];
        $totals = [];
        
        foreach ($result as $row) {
            $labels[] = strtoupper($row['tipe_pembayaran']);
            $values[] = (int)$row['jumlah'];
            $totals[] = (float)$row['total'];
        }
        
        return $this->response->setJSON([
            'labels' => $labels,
            'values' => $values,
            'totals' => $totals
        ]);
    }

    // API untuk Data Dashboard Lengkap
    public function getDashboardData()
    {
        $db = \Config\Database::connect();
        
        // Penjualan hari ini
        $penjualanHariIni = $db->table('transaksi')
            ->select('COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->get()
            ->getRow();
        
        // Penjualan bulan ini
        $penjualanBulanIni = $db->table('transaksi')
            ->select('COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->get()
            ->getRow();
        
        // Rata-rata transaksi per hari bulan ini
        $totalHari = date('t');
        $rataPenjualan = $penjualanBulanIni->total / $totalHari;
        
        return $this->response->setJSON([
            'hari_ini' => [
                'jumlah' => (int)($penjualanHariIni->jumlah ?? 0),
                'total' => (float)($penjualanHariIni->total ?? 0)
            ],
            'bulan_ini' => [
                'jumlah' => (int)($penjualanBulanIni->jumlah ?? 0),
                'total' => (float)($penjualanBulanIni->total ?? 0),
                'rata_harian' => (float)$rataPenjualan
            ]
        ]);
    }
}