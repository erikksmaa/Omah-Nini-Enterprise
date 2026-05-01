<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\WarnaModel;

class Warna extends BaseController
{
    protected $warnaModel;

    public function __construct()
    {
        $this->warnaModel = new WarnaModel();
    }

    public function all()
    {
        $warna = $this->warnaModel->orderBy('nama_warna', 'ASC')->findAll();
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $warna
        ]);
    }
}