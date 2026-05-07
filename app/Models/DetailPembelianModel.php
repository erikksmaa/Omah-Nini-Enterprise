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

    /**
     * Get detail items for a given pembelian ID
     */
    public function getByPembelianId($pembelianId)
    {
        return $this->where('id_pembelian', $pembelianId)->findAll();
    }

    /**
     * Get detail with product info (sku, motif, warna)
     */
    public function getWithProductInfo($pembelianId)
    {
        return $this->select('detail_pembelian.*, produk.sku, produk.foto, motif.nama_motif, warna.nama_warna')
            ->join('produk', 'produk.id = detail_pembelian.id_produk')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->where('detail_pembelian.id_pembelian', $pembelianId)
            ->findAll();
    }
}