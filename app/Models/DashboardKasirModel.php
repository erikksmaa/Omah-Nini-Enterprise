<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardKasirModel extends Model
{
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }
    
    // ========== STATISTIK PENJUALAN ==========
    
    /**
     * Get penjualan hari ini
     */
    public function getPenjualanHariIni()
    {
        $result = $this->db->table('transaksi')
            ->select('COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->get()
            ->getRow();
        
        return [
            'jumlah' => (int)($result->jumlah ?? 0),
            'total' => (float)($result->total ?? 0)
        ];
    }
    
    /**
     * Get penjualan bulan ini
     */
    public function getPenjualanBulanIni()
    {
        $result = $this->db->table('transaksi')
            ->select('COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->get()
            ->getRow();
        
        return [
            'jumlah' => (int)($result->jumlah ?? 0),
            'total' => (float)($result->total ?? 0)
        ];
    }
    
    /**
     * Get rata-rata penjualan per hari
     */
    public function getRataPenjualanHarian()
    {
        $penjualanBulanIni = $this->getPenjualanBulanIni();
        $totalHari = date('t');
        
        return [
            'rata_transaksi' => $totalHari > 0 ? round($penjualanBulanIni['jumlah'] / $totalHari, 1) : 0,
            'rata_omset' => $totalHari > 0 ? round($penjualanBulanIni['total'] / $totalHari, 0) : 0
        ];
    }
    
    /**
     * Get transaksi terbaru
     */
    public function getTransaksiTerbaru($limit = 10)
    {
        return $this->db->table('transaksi')
            ->select('transaksi.*, users.username')
            ->join('users', 'users.user_id = transaksi.id_user', 'left')
            ->where('transaksi.status', 'selesai')
            ->orderBy('transaksi.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
    
    // ========== PRODUK TERLARIS ==========
    
    /**
     * Get produk terlaris hari ini
     */
    public function getProdukTerlarisHariIni($limit = 5)
    {
        return $this->db->table('detail_transaksi')
            ->select('detail_transaksi.nama_produk, SUM(detail_transaksi.jumlah) as total_terjual, SUM(detail_transaksi.subtotal) as total_omset')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('DATE(transaksi.created_at)', date('Y-m-d'))
            ->groupBy('detail_transaksi.nama_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get produk terlaris bulan ini
     */
    public function getProdukTerlarisBulanIni($limit = 5)
    {
        return $this->db->table('detail_transaksi')
            ->select('detail_transaksi.nama_produk, SUM(detail_transaksi.jumlah) as total_terjual, SUM(detail_transaksi.subtotal) as total_omset')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('MONTH(transaksi.created_at)', date('m'))
            ->where('YEAR(transaksi.created_at)', date('Y'))
            ->groupBy('detail_transaksi.nama_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
    
    // ========== METODE PEMBAYARAN ==========
    
    /**
     * Get metode pembayaran hari ini
     */
    public function getMetodePembayaranHariIni()
    {
        return $this->db->table('transaksi')
            ->select('tipe_pembayaran, COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->groupBy('tipe_pembayaran')
            ->get()
            ->getResultArray();
    }
    
    // ========== JAM SIBUK ==========
    
    /**
     * Get jam sibuk (jam dengan transaksi terbanyak)
     */
    public function getJamSibuk()
    {
        return $this->db->table('transaksi')
            ->select('HOUR(created_at) as jam, COUNT(*) as jumlah')
            ->where('status', 'selesai')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->groupBy('HOUR(created_at)')
            ->orderBy('jumlah', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
    }
    
    // ========== STOK MENIPIS ==========
    
    /**
     * Get produk stok menipis
     */
    public function getStokMenipis($limit = 10)
    {
        $produkModel = new ProdukModel();
        return $produkModel
            ->select('produk.id, produk.nama_barang, produk.sku, produk.stok, produk.min_stok')
            ->where('produk.stok <=', 'produk.min_stok', false)
            ->orderBy('produk.stok', 'ASC')
            ->limit($limit)
            ->findAll();
    }
    
    // ========== GRAFIK ==========
    
    /**
     * Get data penjualan 7 hari terakhir untuk chart
     */
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
        
        return ['labels' => $labels, 'values' => $values];
    }
    
    /**
     * Get data metode pembayaran untuk chart
     */
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
        
        return ['labels' => $labels, 'values' => $values];
    }
}