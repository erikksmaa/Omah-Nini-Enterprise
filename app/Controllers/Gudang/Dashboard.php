<?php
namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\PembelianModel;
use App\Models\LogStokModel;
use App\Models\TransaksiModel;
use App\Models\SupplierModel;

class Dashboard extends BaseController
{
    protected $produkModel;
    protected $pembelianModel;
    protected $logStokModel;
    protected $transaksiModel;
    protected $supplierModel;
    protected $db;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }
        
        $role = session()->get('role');
        if ($role != 'gudang' && $role != 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }
        
        $this->db = \Config\Database::connect();
        $this->produkModel = new ProdukModel();
        $this->pembelianModel = new PembelianModel();
        $this->logStokModel = new LogStokModel();
        $this->transaksiModel = new TransaksiModel();
        $this->supplierModel = new SupplierModel();
    }

    public function index()
    {
        // Total produk
        $totalProduk = $this->produkModel->countAll();
        
        // Stok menipis (stok <= min_stok)
        $stokMenipis = $this->produkModel->where('stok <=', 'min_stok', false)->countAllResults();
        $stokHabis = $this->produkModel->where('stok', 0)->countAllResults();
        
        // Total pembelian bulan ini
        $totalPembelianBulanIni = $this->db->table('pembelian')
            ->selectSum('total_harga')
            ->where('MONTH(tanggal_pembelian)', date('m'))
            ->where('YEAR(tanggal_pembelian)', date('Y'))
            ->get()
            ->getRow()
            ->total_harga ?? 0;
        
        $jumlahPembelianBulanIni = $this->db->table('pembelian')
            ->where('MONTH(tanggal_pembelian)', date('m'))
            ->where('YEAR(tanggal_pembelian)', date('Y'))
            ->countAllResults();
        
        // Pembelian terbaru (5 data terakhir)
        $pembelianTerbaru = $this->db->table('pembelian')
            ->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier', 'left')
            ->orderBy('pembelian.id', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
        
        // Produk dengan stok terbanyak
        $produkTerbanyak = $this->produkModel->orderBy('stok', 'DESC')->limit(5)->findAll();
        
        // Produk dengan stok menipis
        $produkMenipis = $this->produkModel->where('stok <=', 'min_stok', false)
            ->orderBy('stok', 'ASC')
            ->limit(10)
            ->findAll();
        
        // Log stok terbaru (aktivitas terakhir)
        $logTerbaru = $this->db->table('log_stok')
            ->select('log_stok.*, produk.nama_barang, produk.sku, users.username')
            ->join('produk', 'produk.id = log_stok.id_produk', 'left')
            ->join('users', 'users.user_id = log_stok.id_user', 'left')
            ->orderBy('log_stok.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        
        // Total nilai stok
        $totalNilaiStok = $this->db->table('produk')
            ->select('SUM(stok * harga_beli) as total')
            ->get()
            ->getRow()
            ->total ?? 0;
        
        // Statistik per supplier
        $statistikSupplier = $this->db->table('pembelian')
            ->select('supplier.nama, COUNT(pembelian.id) as jumlah_pembelian, SUM(pembelian.total_harga) as total_pembelian')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->groupBy('pembelian.id_supplier')
            ->orderBy('total_pembelian', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
        
        $data = [
            'title' => 'Dashboard Gudang',
            'total_produk' => $totalProduk,
            'stok_menipis' => $stokMenipis,
            'stok_habis' => $stokHabis,
            'total_pembelian_bulan_ini' => $totalPembelianBulanIni,
            'jumlah_pembelian_bulan_ini' => $jumlahPembelianBulanIni,
            'pembelian_terbaru' => $pembelianTerbaru,
            'produk_terbanyak' => $produkTerbanyak,
            'produk_menipis' => $produkMenipis,
            'log_terbaru' => $logTerbaru,
            'total_nilai_stok' => $totalNilaiStok,
            'statistik_supplier' => $statistikSupplier
        ];
        
        return view('gudang/dashboard', $data);
    }
}