<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MotifModel;

class Motif extends BaseController
{
    protected $motifModel;

    public function __construct()
    {
        $this->motifModel = new MotifModel();
    }

    /**
     * Get motif by supplier ID
     * @param int $id_supplier
     * @return JSON
     */
    public function getBySupplier($id_supplier)
    {
        // Log untuk debugging
        log_message('debug', 'API getBySupplier called with supplier_id: ' . $id_supplier);
        
        $motif = $this->motifModel->where('id_supplier', $id_supplier)
                                   ->orderBy('nama_motif', 'ASC')
                                   ->findAll();
        
        log_message('debug', 'Found motif count: ' . count($motif));
        
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $motif
        ]);
    }
}