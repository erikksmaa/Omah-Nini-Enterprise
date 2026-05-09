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

    public function getBySupplier($id_supplier)
    {
        $produkModel = new ProdukModel();
        $produk = $produkModel
            ->select('produk.id, motif.nama_motif, warna.nama_warna, produk.stok')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->where('produk.id_supplier', $id_supplier)
            ->orderBy('motif.nama_motif', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $produk
        ]);
    }    
}