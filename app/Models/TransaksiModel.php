<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table            = 'transaksi';
    protected $allowedFields    = ['no_invoice', 'id_user', 'total_bayar', 'tipe_pembayaran', 'status', 'catatan']; // [cite: 20]
}

// DetailTransaksiModel.php
class DetailTransaksiModel extends Model {
    protected $table            = 'detail_transaksi';
    protected $allowedFields    = ['id_transaksi', 'id_produk', 'nama_produk', 'jumlah', 'harga_satuan', 'subtotal'];
}
