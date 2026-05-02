<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProdukModel;

class Search extends BaseController
{
    public function produk()
    {
        $keyword = $this->request->getPost('keyword');
        if (empty($keyword)) {
            return $this->response->setJSON([]);
        }

        $produkModel = new ProdukModel();
        $results = $produkModel->search($keyword, 20);

        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id'   => $row['id'],
                'text' => $row['sku'] . ' - ' . $row['nama_motif'] . ' ' . $row['nama_warna'] . ' (Stok: ' . $row['stok'] . ')',
                'stok' => $row['stok']
            ];
        }

        return $this->response->setJSON($data);
    }
}