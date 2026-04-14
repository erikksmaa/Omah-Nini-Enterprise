<?php

namespace App\Models;

use CodeIgniter\Model;

class PembelianModel extends Model
{
    protected $table            = 'pembelian';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'no_invoice', 'id_supplier', 'id_user', 
        'tanggal_pembelian', 'total_harga', 'catatan'
    ];
    protected $useTimestamps    = true;
}
