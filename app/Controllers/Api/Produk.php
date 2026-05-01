<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProdukModel;

class Produk extends BaseController
{
    protected $produkModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
    }

    public function getByMotif($id_motif)
    {
        $produk = $this->produkModel->select('produk.id, produk.sku, warna.nama_warna, produk.stok')
            ->join('warna', 'warna.id = produk.id_warna')
            ->where('produk.id_motif', $id_motif)
            ->where('produk.stok >', 0)
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $produk
        ]);
    }

    
}