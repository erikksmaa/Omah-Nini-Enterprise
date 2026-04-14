<?php

namespace App\Models;

use CodeIgniter\Model;

class LogStokModel extends Model
{
  protected $table            = 'log_stok';
    protected $allowedFields    = [
        'id_produk', 'id_user', 'tipe_ref', 'id_ref', 
        'jumlah_sebelum', 'jumlah_perubahan', 'jumlah_sesudah', 'aktivitas'
    ]; // 
    protected $useTimestamps    = true;
    protected $updatedField     = ''; // Log tidak boleh diupdate
}
