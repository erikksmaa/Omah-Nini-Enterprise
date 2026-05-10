<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPembelianModel extends Model
{
    protected $table = 'detail_pembelian';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_pembelian',
        'id_produk',
        'nama_produk',
        'jumlah'
    ];

    protected $useTimestamps = false;

    /**
     * Get details by pembelian ID
     */
    public function getByPembelian($pembelianId)
    {
        return $this->where('id_pembelian', $pembelianId)->findAll();
    }

    /**
     * Get details with product info (including foto)
     */
    public function getWithProductInfo($pembelianId)
    {
        return $this->select('detail_pembelian.*, produk.sku, produk.foto, motif.nama_motif, warna.nama_warna, supplier.nama as nama_supplier')
            ->join('produk', 'produk.id = detail_pembelian.id_produk')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->join('supplier', 'supplier.id = produk.id_supplier')
            ->where('detail_pembelian.id_pembelian', $pembelianId)
            ->findAll();
    }

    /**
     * Delete all details by pembelian ID (for rollback/cancel)
     */
    public function deleteByPembelian($pembelianId)
    {
        return $this->where('id_pembelian', $pembelianId)->delete();
    }
}