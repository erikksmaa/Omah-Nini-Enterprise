<?php
namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;

class Dashboard extends BaseController
{
    protected $produkModel;
    protected $transaksiModel;
    protected $detailTransaksiModel;
    protected $db;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }
        
        $role = session()->get('role');
        if ($role != 'kasir' && $role != 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }
        
        $this->db = \Config\Database::connect();
        $this->produkModel = new ProdukModel();
        $this->transaksiModel = new TransaksiModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
    }

    public function index()
    {
        // ========== STATISTIK PENJUALAN HARI INI ==========
        $penjualanHariIni = $this->db->table('transaksi')
            ->select('COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->get()
            ->getRow();
        
        $jumlahTransaksiHariIni = $penjualanHariIni->jumlah ?? 0;
        $omsetHariIni = $penjualanHariIni->total ?? 0;
        
        // ========== STATISTIK PENJUALAN BULAN INI ==========
        $penjualanBulanIni = $this->db->table('transaksi')
            ->select('COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->get()
            ->getRow();
        
        $jumlahTransaksiBulanIni = $penjualanBulanIni->jumlah ?? 0;
        $omsetBulanIni = $penjualanBulanIni->total ?? 0;
        
        // ========== RATA-RATA PER HARI ==========
        $totalHari = date('t');
        $rataTransaksiHarian = $totalHari > 0 ? round($jumlahTransaksiBulanIni / $totalHari, 1) : 0;
        $rataOmsetHarian = $totalHari > 0 ? round($omsetBulanIni / $totalHari, 0) : 0;
        
        // ========== TRANSAKSI TERBARU ==========
        $transaksiTerbaru = $this->db->table('transaksi')
            ->select('transaksi.*, users.username')
            ->join('users', 'users.user_id = transaksi.id_user', 'left')
            ->where('transaksi.status', 'selesai')
            ->orderBy('transaksi.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        
        // ========== PRODUK TERLARIS HARI INI ==========
        $produkTerlarisHariIni = $this->db->table('detail_transaksi')
            ->select('detail_transaksi.nama_produk, SUM(detail_transaksi.jumlah) as total_terjual, SUM(detail_transaksi.subtotal) as total_omset')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('DATE(transaksi.created_at)', date('Y-m-d'))
            ->groupBy('detail_transaksi.nama_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
        
        // ========== PRODUK TERLARIS BULAN INI ==========
        $produkTerlarisBulanIni = $this->db->table('detail_transaksi')
            ->select('detail_transaksi.nama_produk, SUM(detail_transaksi.jumlah) as total_terjual, SUM(detail_transaksi.subtotal) as total_omset')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('MONTH(transaksi.created_at)', date('m'))
            ->where('YEAR(transaksi.created_at)', date('Y'))
            ->groupBy('detail_transaksi.nama_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
        
        // ========== METODE PEMBAYARAN HARI INI ==========
        $metodePembayaran = $this->db->table('transaksi')
            ->select('tipe_pembayaran, COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->groupBy('tipe_pembayaran')
            ->get()
            ->getResultArray();
        
        // ========== JAM SIBUK (Jam berapa paling banyak transaksi) ==========
        $jamSibuk = $this->db->table('transaksi')
            ->select('HOUR(created_at) as jam, COUNT(*) as jumlah')
            ->where('status', 'selesai')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->groupBy('HOUR(created_at)')
            ->orderBy('jumlah', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
        
        // ========== PRODUK STOK MENIPIS ==========
        $stokMenipis = $this->produkModel
            ->select('produk.id, produk.nama_barang, produk.sku, produk.stok, produk.min_stok')
            ->where('produk.stok <=', 'produk.min_stok', false)
            ->orderBy('produk.stok', 'ASC')
            ->limit(10)
            ->findAll();
        
        $data = [
            'title' => 'Dashboard Kasir',
            'username' => session()->get('username'),
            'role' => session()->get('role'),
            // Statistik hari ini
            'jumlah_transaksi_hari_ini' => $jumlahTransaksiHariIni,
            'omset_hari_ini' => $omsetHariIni,
            // Statistik bulan ini
            'jumlah_transaksi_bulan_ini' => $jumlahTransaksiBulanIni,
            'omset_bulan_ini' => $omsetBulanIni,
            // Rata-rata
            'rata_transaksi_harian' => $rataTransaksiHarian,
            'rata_omset_harian' => $rataOmsetHarian,
            // Data lainnya
            'transaksi_terbaru' => $transaksiTerbaru,
            'produk_terlaris_hari_ini' => $produkTerlarisHariIni,
            'produk_terlaris_bulan_ini' => $produkTerlarisBulanIni,
            'metode_pembayaran' => $metodePembayaran,
            'jam_sibuk' => $jamSibuk,
            'stok_menipis' => $stokMenipis
        ];
        
        return view('kasir/dashboard', $data);
    }
    
    // API untuk Chart Penjualan 7 Hari Terakhir (Kasir)
    public function getWeeklySalesChart()
    {
        $result = $this->db->table('transaksi')
            ->select('DATE(created_at) as tanggal, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
            ->groupBy('DATE(created_at)')
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();
        
        $labels = [];
        $values = [];
        
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
    
    // API untuk Chart Metode Pembayaran (Kasir)
    public function getPaymentMethodChart()
    {
        $result = $this->db->table('transaksi')
            ->select('tipe_pembayaran, COUNT(*) as jumlah')
            ->where('status', 'selesai')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->groupBy('tipe_pembayaran')
            ->get()
            ->getResultArray();
        
        $labels = [];
        $values = [];
        
        foreach ($result as $row) {
            $labels[] = strtoupper($row['tipe_pembayaran']);
            $values[] = (int)$row['jumlah'];
        }
        
        return $this->response->setJSON([
            'labels' => $labels,
            'values' => $values
        ]);
    }
}