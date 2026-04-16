<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'sku',
        'nama_barang',
        'id_kategori',
        'id_supplier',
        'harga_beli',
        'harga_jual',
        'stok',
        'min_stok',
        'keterangan',
        'barcode'
    ];
    protected $useTimestamps = true;
}
