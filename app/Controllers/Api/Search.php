<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProdukModel;

class Search extends BaseController
{
    protected $produkModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
    }

    public function produk()
    {
        $keyword = $this->request->getPost('keyword');
        
        $produk = $this->produkModel->select('produk.id, produk.sku, motif.nama_motif, warna.nama_warna, produk.stok')
                                    ->join('motif', 'motif.id = produk.id_motif')
                                    ->join('warna', 'warna.id = produk.id_warna')
                                    ->groupStart()
                                        ->like('produk.sku', $keyword)
                                        ->orLike('motif.nama_motif', $keyword)
                                        ->orLike('warna.nama_warna', $keyword)
                                    ->groupEnd()
                                    ->where('produk.stok >', 0)
                                    ->limit(10)
                                    ->findAll();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $produk
        ]);
    }
}