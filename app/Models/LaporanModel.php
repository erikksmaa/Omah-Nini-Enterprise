<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }
    
    // ========== HELPER ==========
    
    /**
     * Get list bulan
     */
    public function getBulanList()
    {
        return [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
    }
    
    /**
     * Get list tahun
     */
    public function getTahunList()
    {
        $currentYear = date('Y');
        $tahun = [];
        for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
            $tahun[$i] = $i;
        }
        return $tahun;
    }
    
    // ========== LAPORAN KEUANGAN ==========
    
    /**
     * Get total pemasukan per bulan/tahun
     */
    public function getTotalPemasukan($bulan, $tahun)
    {
        $keuanganModel = new KeuanganModel();
        return $keuanganModel
            ->where('tipe', 'pemasukan')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->selectSum('jumlah')
            ->first()['jumlah'] ?? 0;
    }
    
    /**
     * Get total pengeluaran per bulan/tahun
     */
    public function getTotalPengeluaran($bulan, $tahun)
    {
        $keuanganModel = new KeuanganModel();
        return $keuanganModel
            ->where('tipe', 'pengeluaran')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->selectSum('jumlah')
            ->first()['jumlah'] ?? 0;
    }
    
    /**
     * Get pemasukan per kategori
     */
    public function getPemasukanByKategori($bulan, $tahun)
    {
        $keuanganModel = new KeuanganModel();
        return $keuanganModel
            ->select('kategori, SUM(jumlah) as total')
            ->where('tipe', 'pemasukan')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->groupBy('kategori')
            ->findAll();
    }
    
    /**
     * Get pengeluaran per kategori
     */
    public function getPengeluaranByKategori($bulan, $tahun)
    {
        $keuanganModel = new KeuanganModel();
        return $keuanganModel
            ->select('kategori, SUM(jumlah) as total')
            ->where('tipe', 'pengeluaran')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->groupBy('kategori')
            ->findAll();
    }
    
    /**
     * Get detail keuangan dengan user
     */
    public function getDetailKeuangan($bulan, $tahun)
    {
        $keuanganModel = new KeuanganModel();
        return $keuanganModel
            ->select('keuangan.*, users.username')
            ->join('users', 'users.user_id = keuangan.id_user', 'left')
            ->where('MONTH(keuangan.tanggal_transaksi)', $bulan)
            ->where('YEAR(keuangan.tanggal_transaksi)', $tahun)
            ->orderBy('keuangan.tanggal_transaksi', 'DESC')
            ->findAll();
    }
    
    // ========== LAPORAN PENJUALAN ==========
    
    /**
     * Get data penjualan dengan filter tanggal
     */
    public function getPenjualanByDate($startDate, $endDate)
    {
        $transaksiModel = new TransaksiModel();
        return $transaksiModel
            ->select('transaksi.*, users.username')
            ->join('users', 'users.user_id = transaksi.id_user', 'left')
            ->where('transaksi.status', 'selesai')
            ->where('transaksi.created_at >=', $startDate . ' 00:00:00')
            ->where('transaksi.created_at <=', $endDate . ' 23:59:59')
            ->orderBy('transaksi.created_at', 'DESC')
            ->findAll();
    }
    
    /**
     * Get total item terjual
     */
    public function getTotalItemTerjual($startDate, $endDate)
    {
        return $this->db->table('detail_transaksi')
            ->select('SUM(detail_transaksi.jumlah) as total')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('transaksi.created_at >=', $startDate . ' 00:00:00')
            ->where('transaksi.created_at <=', $endDate . ' 23:59:59')
            ->get()
            ->getRow()
            ->total ?? 0;
    }
    
    /**
     * Get penjualan per hari
     */
    public function getPenjualanPerHari($startDate, $endDate)
    {
        return $this->db->table('transaksi')
            ->select('DATE(created_at) as tanggal, COUNT(*) as jumlah_transaksi, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->groupBy('DATE(created_at)')
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get produk terlaris
     */
    public function getProdukTerlaris($startDate, $endDate, $limit = 10)
    {
        return $this->db->table('detail_transaksi')
            ->select('detail_transaksi.id_produk, detail_transaksi.nama_produk, SUM(detail_transaksi.jumlah) as total_terjual, SUM(detail_transaksi.subtotal) as total_omset')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('transaksi.created_at >=', $startDate . ' 00:00:00')
            ->where('transaksi.created_at <=', $endDate . ' 23:59:59')
            ->groupBy('detail_transaksi.id_produk, detail_transaksi.nama_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
    
    // ========== LAPORAN PEMBELIAN ==========
    
    /**
     * Get data pembelian dengan filter tanggal
     */
    public function getPembelianByDate($startDate, $endDate)
    {
        $pembelianModel = new PembelianModel();
        return $pembelianModel
            ->select('pembelian.*, supplier.nama as supplier_nama, users.username')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->join('users', 'users.user_id = pembelian.id_user', 'left')
            ->where('pembelian.tanggal_pembelian >=', $startDate)
            ->where('pembelian.tanggal_pembelian <=', $endDate)
            ->orderBy('pembelian.tanggal_pembelian', 'DESC')
            ->findAll();
    }
    
    /**
     * Get pembelian per supplier
     */
    public function getPembelianPerSupplier($startDate, $endDate)
    {
        return $this->db->table('pembelian')
            ->select('supplier.nama as supplier_nama, COUNT(*) as jumlah_transaksi, SUM(pembelian.total_harga) as total')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->where('pembelian.tanggal_pembelian >=', $startDate)
            ->where('pembelian.tanggal_pembelian <=', $endDate)
            ->groupBy('pembelian.id_supplier')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    // ========== LAPORAN LABA/RUGI ==========
    
    /**
     * Get total penjualan untuk laba/rugi
     */
    public function getTotalPenjualanForLabaRugi($startDate, $endDate)
    {
        $transaksiModel = new TransaksiModel();
        return $transaksiModel
            ->where('status', 'selesai')
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->selectSum('total_bayar')
            ->first()['total_bayar'] ?? 0;
    }
    
    /**
     * Get HPP (Harga Pokok Penjualan)
     */
    public function getHpp($startDate, $endDate)
    {
        return $this->db->table('detail_transaksi')
            ->select('SUM(detail_transaksi.jumlah * produk.harga_beli) as total_hpp')
            ->join('produk', 'produk.id = detail_transaksi.id_produk')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('transaksi.created_at >=', $startDate . ' 00:00:00')
            ->where('transaksi.created_at <=', $endDate . ' 23:59:59')
            ->get()
            ->getRow()
            ->total_hpp ?? 0;
    }
    
    /**
     * Get biaya operasional
     */
    public function getBiayaOperasional($bulan, $tahun)
    {
        $keuanganModel = new KeuanganModel();
        return $keuanganModel
            ->where('tipe', 'pengeluaran')
            ->where('kategori !=', 'pembelian')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->selectSum('jumlah')
            ->first()['jumlah'] ?? 0;
    }
    
    /**
     * Get total pembelian untuk laba/rugi
     */
    public function getTotalPembelianForLabaRugi($bulan, $tahun)
    {
        $pembelianModel = new PembelianModel();
        return $pembelianModel
            ->where('MONTH(tanggal_pembelian)', $bulan)
            ->where('YEAR(tanggal_pembelian)', $tahun)
            ->selectSum('total_harga')
            ->first()['total_harga'] ?? 0;
    }
    
    // ========== LAPORAN STOK & PRODUK ==========
    
    /**
     * Get produk dengan stok terbanyak
     */
    public function getStokTerbanyak($limit = 10)
    {
        $produkModel = new ProdukModel();
        return $produkModel
            ->select('produk.*, kategori.nama as kategori_nama')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->orderBy('stok', 'DESC')
            ->limit($limit)
            ->findAll();
    }
    
    /**
     * Get total nilai stok
     */
    public function getTotalNilaiStok()
    {
        return $this->db->table('produk')
            ->select('SUM(stok * harga_beli) as total')
            ->get()
            ->getRow()
            ->total ?? 0;
    }
    
    /**
     * Get total produk count
     */
    public function getTotalProduk()
    {
        $produkModel = new ProdukModel();
        return $produkModel->countAll();
    }
    
    /**
     * Get produk terlaris all time
     */
    public function getProdukTerlarisAllTime($limit = 10)
    {
        return $this->db->table('detail_transaksi')
            ->select('detail_transaksi.id_produk, detail_transaksi.nama_produk, SUM(detail_transaksi.jumlah) as total_terjual, SUM(detail_transaksi.subtotal) as total_omset')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->groupBy('detail_transaksi.id_produk, detail_transaksi.nama_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get produk tidak pernah terjual (dengan pagination)
     */
    public function getProdukNeverSoldPaginated($perPage = 10)
    {
        // Ambil ID produk yang sudah terjual
        $terjual = $this->db->table('detail_transaksi')
            ->select('id_produk')
            ->groupBy('id_produk')
            ->get()
            ->getResultArray();
        
        $terjualIds = array_column($terjual, 'id_produk');
        
        $produkModel = new ProdukModel();
        $builder = $produkModel
            ->select('produk.id, produk.nama_barang, produk.sku, produk.stok, produk.harga_beli')
            ->orderBy('produk.nama_barang', 'ASC');
        
        if (!empty($terjualIds)) {
            $builder->whereNotIn('produk.id', $terjualIds);
        }
        
        return $builder->paginate($perPage);
    }
    
    /**
     * Get pager untuk produk never sold
     */
    public function getPagerNeverSold()
    {
        $produkModel = new ProdukModel();
        return $produkModel->pager;
    }
    
    /**
     * Get statistik lengkap laporan produk
     */
    public function getLaporanProdukStatistik()
    {
        return [
            'total_produk' => $this->getTotalProduk(),
            'total_nilai_stok' => $this->getTotalNilaiStok()
        ];
    }
}