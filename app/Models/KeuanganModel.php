<?php

namespace App\Models;

use CodeIgniter\Model;

class KeuanganModel extends Model
{
    protected $table            = 'keuangan';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id_user', 'tipe', 'kategori', 'tipe_ref', 
        'id_ref', 'jumlah', 'tanggal_transaksi'
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; // Data keuangan bersifat permane
}
