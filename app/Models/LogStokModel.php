<?php

namespace App\Models;

use CodeIgniter\Model;

class LogStokModel extends Model
{
    protected $table = 'log_stok';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_produk', 'id_user', 'tipe_ref', 'id_ref',
        'jumlah_sebelum', 'jumlah_perubahan', 'jumlah_sesudah', 'aktivitas', 'created_at'
    ];
    protected $useTimestamps = false;
    public function getWithProduk($limit = 10)
{
    return $this->select('log_stok.*, produk.nama_barang, produk.sku, users.username')
                ->join('produk', 'produk.id = log_stok.id_produk', 'left')
                ->join('users', 'users.user_id = log_stok.id_user', 'left')
                ->orderBy('log_stok.created_at', 'DESC')
                ->limit($limit)
                ->findAll();
}
}
