<?php
namespace App\Models;

use CodeIgniter\Model;

class LogStokModel extends Model
{
    protected $table = 'log_stok';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_produk',
        'id_user',
        'tipe_ref',
        'id_ref',
        'jumlah_sebelum',
        'jumlah_perubahan',
        'jumlah_sesudah',
        'created_at'
    ];

    // Method tambahan bisa ditambahkan nanti untuk laporan
}