<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardGudangModel extends Model
{
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }
    
    /**
     * Get total produk
     */
    public function getTotalProduk()
    {
        $produkModel = new ProdukModel();
        return $produkModel->countAll();
    }
    
    /**
     * Get jumlah stok menipis
     */
    public function getStokMenipis()
    {
        $produkModel = new ProdukModel();
        return $produkModel->where('stok <=', 'min_stok', false)->countAllResults();
    }
    
    /**
     * Get jumlah stok habis
     */
    public function getStokHabis()
    {
        $produkModel = new ProdukModel();
        return $produkModel->where('stok', 0)->countAllResults();
    }
    
    /**
     * Get total pembelian bulan ini
     */
    public function getTotalPembelianBulanIni()
    {
        return $this->db->table('pembelian')
            ->selectSum('total_harga')
            ->where('MONTH(tanggal_pembelian)', date('m'))
            ->where('YEAR(tanggal_pembelian)', date('Y'))
            ->get()
            ->getRow()
            ->total_harga ?? 0;
    }
    
    /**
     * Get jumlah pembelian bulan ini
     */
    public function getJumlahPembelianBulanIni()
    {
        return $this->db->table('pembelian')
            ->where('MONTH(tanggal_pembelian)', date('m'))
            ->where('YEAR(tanggal_pembelian)', date('Y'))
            ->countAllResults();
    }
    
    /**
     * Get pembelian terbaru
     */
    public function getPembelianTerbaru($limit = 5)
    {
        return $this->db->table('pembelian')
            ->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier', 'left')
            ->orderBy('pembelian.id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get produk dengan stok terbanyak
     */
    public function getProdukStokTerbanyak($limit = 5)
    {
        $produkModel = new ProdukModel();
        return $produkModel->orderBy('stok', 'DESC')->limit($limit)->findAll();
    }
    
    /**
     * Get produk dengan stok menipis
     */
    public function getProdukStokMenipis($limit = 10)
    {
        $produkModel = new ProdukModel();
        return $produkModel->where('stok <=', 'min_stok', false)
            ->orderBy('stok', 'ASC')
            ->limit($limit)
            ->findAll();
    }
    
    /**
     * Get log stok terbaru
     */
    public function getLogStokTerbaru($limit = 10)
    {
        return $this->db->table('log_stok')
            ->select('log_stok.*, produk.nama_barang, produk.sku, users.username')
            ->join('produk', 'produk.id = log_stok.id_produk', 'left')
            ->join('users', 'users.user_id = log_stok.id_user', 'left')
            ->orderBy('log_stok.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
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
     * Get statistik per supplier
     */
    public function getStatistikSupplier($limit = 5)
    {
        return $this->db->table('pembelian')
            ->select('supplier.nama, COUNT(pembelian.id) as jumlah_pembelian, SUM(pembelian.total_harga) as total_pembelian')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->groupBy('pembelian.id_supplier')
            ->orderBy('total_pembelian', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}