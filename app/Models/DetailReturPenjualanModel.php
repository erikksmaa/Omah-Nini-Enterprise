<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailReturPenjualanModel extends Model
{
    protected $table = 'detail_retur_penjualan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = false;
    
    protected $allowedFields = [
        'id_retur',
        'id_detail_transaksi',
        'id_produk',
        'nama_produk',
        'jumlah',
        'harga_jual',
        'subtotal'
    ];
    
    // Get detail by id_retur
    public function getByRetur($id_retur)
    {
        return $this->where('id_retur', $id_retur)->findAll();
    }
}