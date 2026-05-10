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
        $keyword = $this->request->getGet('keyword');

        if (empty($keyword) || strlen($keyword) < 2) {
            return $this->response->setJSON([]);
        }

        $results = $this->produkModel->select('produk.id, produk.sku, produk.foto, produk.stok, motif.nama_motif, warna.nama_warna')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->groupStart()
            ->like('produk.sku', $keyword)
            ->orLike('motif.nama_motif', $keyword)
            ->orLike('warna.nama_warna', $keyword)
            ->groupEnd()
            ->where('produk.stok >', 0)
            ->limit(20)
            ->findAll();

        $data = [];
        foreach ($results as $row) {
            // Buat URL lengkap untuk foto
            $fotoUrl = !empty($row['foto']) ? base_url('uploads/produk/' . $row['foto']) : base_url('assets/img/no-image.png');

            $data[] = [
                'id' => $row['id'],
                'text' => $row['sku'] . ' - ' . $row['nama_motif'] . ' ' . $row['nama_warna'] . ' (Stok: ' . $row['stok'] . ')',
                'stok' => $row['stok'],
                'foto' => $fotoUrl
            ];
        }

        return $this->response->setJSON($data);
    }

    
}