<?php
namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table = 'detail_transaksi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_transaksi',
        'id_produk',
        'nama_produk',
        'jumlah'
    ];

    public function getByTransaksiId($transaksiId)
    {
        return $this->where('id_transaksi', $transaksiId)->findAll();
    }

    public function getWithProductInfo($transaksiId)
    {
        return $this->select('detail_transaksi.*, produk.sku, motif.nama_motif, warna.nama_warna')
                    ->join('produk', 'produk.id = detail_transaksi.id_produk')
                    ->join('motif', 'motif.id = produk.id_motif')
                    ->join('warna', 'warna.id = produk.id_warna')
                    ->where('detail_transaksi.id_transaksi', $transaksiId)
                    ->findAll();
    }
}