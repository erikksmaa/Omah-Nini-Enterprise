<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPembelianModel extends Model
{
    protected $table = 'detail_pembelian';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_pembelian',
        'id_produk',
        'nama_produk',
        'jumlah'
    ];

    /**
     * Get detail items for a given pembelian ID
     */
    public function getByPembelianId($pembelianId)
    {
        return $this->where('id_pembelian', $pembelianId)->findAll();
    }
}