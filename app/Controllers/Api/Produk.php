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


    /**
     * Get produk by supplier ID (untuk dropdown pembelian)
     */
    public function getBySupplier($supplierId)
    {
        $produk = $this->produkModel->select('produk.id, produk.stok, produk.foto, motif.nama_motif, warna.nama_warna')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->where('produk.id_supplier', $supplierId)
            ->orderBy('motif.nama_motif', 'ASC')
            ->findAll();


        // Format response dengan URL foto lengkap
        $data = [];
        foreach ($produk as $p) {
            $fotoUrl = !empty($p['foto']) ? base_url('uploads/produk/' . $p['foto']) : base_url('assets/img/no-image.png');

            $data[] = [
                'id' => $p['id'],
                'nama_motif' => $p['nama_motif'],
                'nama_warna' => $p['nama_warna'],
                'stok' => $p['stok'],
                'foto' => $fotoUrl
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
