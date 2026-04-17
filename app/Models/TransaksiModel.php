<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'no_invoice',
        'id_user',
        'tanggal_transaksi',
        'total_bayar',
        'tipe_pembayaran',
        'status',
        'catatan',
        'created_at'
    ];

    // Matikan auto timestamps agar kita bisa set manual
    protected $useTimestamps = false;
}

