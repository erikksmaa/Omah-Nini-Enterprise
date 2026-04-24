<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardAdminModel extends Model
{
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }
    
    // ========== STATISTIK DASAR ==========
    
    /**
     * Get total produk
     */
    public function getTotalProduk()
    {
        return $this->db->table('produk')->countAll();
    }
    
    /**
     * Get total kategori
     */
    public function getTotalKategori()
    {
        return $this->db->table('kategori')->countAll();
    }
    
    /**
     * Get total supplier
     */
    public function getTotalSupplier()
    {
        return $this->db->table('supplier')->countAll();
    }
    
    /**
     * Get total user
     */
    public function getTotalUser()
    {
        return $this->db->table('users')->countAll();
    }
    
    /**
     * Get jumlah stok menipis (stok <= min_stok)
     */
    public function getStokMenipis()
    {
        return $this->db->table('produk')
            ->where('stok <=', 'min_stok', false)
            ->countAllResults();
    }
    
    /**
     * Get jumlah stok habis (stok = 0)
     */
    public function getStokHabis()
    {
        return $this->db->table('produk')
            ->where('stok', 0)
            ->countAllResults();
    }
    
    // ========== GRAFIK PENJUALAN ==========
    
    /**
     * Get data penjualan 7 hari terakhir
     * @return array ['labels' => [], 'values' => []]
     */
    public function getWeeklySales($startDate = null, $endDate = null)
    {
        if (!$startDate) {
            $startDate = date('Y-m-d', strtotime('-7 days'));
        }
        if (!$endDate) {
            $endDate = date('Y-m-d');
        }
        
        $result = $this->db->table('transaksi')
            ->select('DATE(created_at) as tanggal, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
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
        
        return [
            'labels' => $labels,
            'values' => $values
        ];
    }
    
    /**
     * Get data penjualan 12 bulan terakhir
     * @param int $year Tahun (default: tahun ini)
     * @return array ['labels' => [], 'values' => []]
     */
    public function getMonthlySales($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $bulanNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $values = array_fill(0, 12, 0);
        
        $result = $this->db->table('transaksi')
            ->select('MONTH(created_at) as bulan, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('YEAR(created_at)', $year)
            ->groupBy('MONTH(created_at)')
            ->get()
            ->getResultArray();
        
        foreach ($result as $row) {
            $values[$row['bulan'] - 1] = (float)$row['total'];
        }
        
        return [
            'labels' => $bulanNames,
            'values' => $values
        ];
    }
    
    // ========== GRAFIK LABA/RUGI ==========
    
    /**
     * Get data laba/rugi bulan ini
     * @param int $month Bulan (default: bulan ini)
     * @param int $year Tahun (default: tahun ini)
     * @return array ['pemasukan' => 0, 'pengeluaran' => 0, 'laba' => 0]
     */
    public function getProfitLoss($month = null, $year = null)
    {
        if (!$month) {
            $month = date('m');
        }
        if (!$year) {
            $year = date('Y');
        }
        
        $pemasukan = $this->db->table('keuangan')
            ->selectSum('jumlah')
            ->where('tipe', 'pemasukan')
            ->where('MONTH(tanggal_transaksi)', $month)
            ->where('YEAR(tanggal_transaksi)', $year)
            ->get()
            ->getRow()
            ->jumlah ?? 0;
        
        $pengeluaran = $this->db->table('keuangan')
            ->selectSum('jumlah')
            ->where('tipe', 'pengeluaran')
            ->where('MONTH(tanggal_transaksi)', $month)
            ->where('YEAR(tanggal_transaksi)', $year)
            ->get()
            ->getRow()
            ->jumlah ?? 0;
        
        return [
            'pemasukan' => (float)$pemasukan,
            'pengeluaran' => (float)$pengeluaran,
            'laba' => (float)($pemasukan - $pengeluaran)
        ];
    }
    
    // ========== DATA STOK ==========
    
    /**
     * Get data produk dengan stok menipis
     * @param int $limit Jumlah data maksimal
     * @return array
     */
    public function getLowStockProducts($limit = 10)
    {
        return $this->db->table('produk')
            ->select('produk.id, produk.nama_barang, produk.sku, produk.stok, produk.min_stok, kategori.nama as kategori')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->where('produk.stok <=', 'produk.min_stok', false)
            ->orderBy('produk.stok', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
    
    // ========== PRODUK TERLARIS ==========
    
    /**
     * Get top produk terlaris
     * @param int $month Bulan (default: bulan ini)
     * @param int $year Tahun (default: tahun ini)
     * @param int $limit Jumlah data maksimal
     * @return array ['labels' => [], 'values' => []]
     */
    public function getTopProducts($month = null, $year = null, $limit = 5)
    {
        if (!$month) {
            $month = date('m');
        }
        if (!$year) {
            $year = date('Y');
        }
        
        $produk = $this->db->table('detail_transaksi')
            ->select('detail_transaksi.nama_produk, SUM(detail_transaksi.jumlah) as total_terjual')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('MONTH(transaksi.created_at)', $month)
            ->where('YEAR(transaksi.created_at)', $year)
            ->groupBy('detail_transaksi.nama_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
        
        return [
            'labels' => array_column($produk, 'nama_produk'),
            'values' => array_column($produk, 'total_terjual')
        ];
    }
    
    // ========== METODE PEMBAYARAN ==========
    
    /**
     * Get statistik metode pembayaran
     * @param int $month Bulan (default: bulan ini)
     * @param int $year Tahun (default: tahun ini)
     * @return array ['labels' => [], 'values' => [], 'totals' => []]
     */
    public function getPaymentMethods($month = null, $year = null)
    {
        if (!$month) {
            $month = date('m');
        }
        if (!$year) {
            $year = date('Y');
        }
        
        $result = $this->db->table('transaksi')
            ->select('tipe_pembayaran, COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('MONTH(created_at)', $month)
            ->where('YEAR(created_at)', $year)
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
        
        return [
            'labels' => $labels,
            'values' => $values,
            'totals' => $totals
        ];
    }
    
    // ========== DASHBOARD DATA LENGKAP ==========
    
    /**
     * Get data dashboard lengkap (penjualan hari ini & bulan ini)
     * @return array
     */
    public function getDashboardData()
    {
        // Penjualan hari ini
        $penjualanHariIni = $this->db->table('transaksi')
            ->select('COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->get()
            ->getRow();
        
        // Penjualan bulan ini
        $penjualanBulanIni = $this->db->table('transaksi')
            ->select('COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->get()
            ->getRow();
        
        // Rata-rata transaksi per hari bulan ini
        $totalHari = date('t');
        $rataPenjualan = $penjualanBulanIni->total / $totalHari;
        
        return [
            'hari_ini' => [
                'jumlah' => (int)($penjualanHariIni->jumlah ?? 0),
                'total' => (float)($penjualanHariIni->total ?? 0)
            ],
            'bulan_ini' => [
                'jumlah' => (int)($penjualanBulanIni->jumlah ?? 0),
                'total' => (float)($penjualanBulanIni->total ?? 0),
                'rata_harian' => (float)$rataPenjualan
            ]
        ];
    }
}