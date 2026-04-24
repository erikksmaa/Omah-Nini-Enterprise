<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailReturPenjualanModel extends Model
{
    protected $table = 'detail_retur_penjualan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = false;
    
    protected $allowedFields = [
        'id_retur',
        'id_detail_transaksi',
        'id_produk',
        'nama_produk',
        'jumlah',
        'harga_jual',
        'subtotal'
    ];
    
    // ========== GET METHODS ==========
    
    /**
     * Get detail by retur ID
     */
    public function getByRetur($returId)
    {
        return $this->where('id_retur', $returId)->findAll();
    }
    
    /**
     * Get detail with product info
     */
    public function getByReturWithProduct($returId)
    {
        $db = \Config\Database::connect();
        return $db->table('detail_retur_penjualan')
            ->select('detail_retur_penjualan.*, produk.sku, produk.nama_barang')
            ->join('produk', 'produk.id = detail_retur_penjualan.id_produk', 'left')
            ->where('id_retur', $returId)
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get detail for export
     */
    public function getForExport($startDate, $endDate)
    {
        $db = \Config\Database::connect();
        return $db->table('detail_retur_penjualan')
            ->select('detail_retur_penjualan.*, retur_penjualan.no_retur')
            ->join('retur_penjualan', 'retur_penjualan.id = detail_retur_penjualan.id_retur')
            ->where('retur_penjualan.tanggal_retur >=', $startDate)
            ->where('retur_penjualan.tanggal_retur <=', $endDate)
            ->get()
            ->getResultArray();
    }
}