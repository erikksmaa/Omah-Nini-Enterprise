<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table = 'detail_transaksi';
    protected $allowedFields = ['id_transaksi', 'id_produk', 'nama_produk', 'jumlah', 'harga_satuan', 'subtotal']; // [cite: 21]
}
