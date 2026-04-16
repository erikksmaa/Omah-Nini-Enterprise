<?php

namespace App\Models;

use CodeIgniter\Model;


class DetailPembelianModel extends Model
{
    protected $table = 'detail_pembelian';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_pembelian', 'id_produk', 'nama_produk',
        'jumlah', 'harga_beli', 'subtotal'
    ];
    protected $useTimestamps = false;

    public function getByPembelian($pembelianId)
    {
        return $this->where('id_pembelian', $pembelianId)->findAll();
    }
}