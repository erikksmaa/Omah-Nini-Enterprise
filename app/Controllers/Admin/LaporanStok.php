<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LogStokModel;
use App\Models\ProdukModel;

class LaporanStok extends BaseController
{
    protected $logStokModel;
    protected $produkModel;

    public function __construct()
    {
        $redirect = $this->checkRole(['admin']);
        if ($redirect) die($redirect);

        $this->logStokModel = new LogStokModel();
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $produkId = $this->request->getGet('produk_id');

        // Query builder dengan join ke produk dan users
        $builder = $this->logStokModel
            ->select('log_stok.*, produk.nama_barang, produk.sku, users.username')
            ->join('produk', 'produk.id = log_stok.id_produk')
            ->join('users', 'users.user_id = log_stok.id_user', 'left')
            ->where('DATE(log_stok.created_at) >=', $startDate)
            ->where('DATE(log_stok.created_at) <=', $endDate);

        if (!empty($produkId)) {
            $builder->where('log_stok.id_produk', $produkId);
        }

        $logStok = $builder->orderBy('log_stok.id', 'DESC')->findAll();

        $data = [
            'title' => 'Audit Trail - Log Stok',
            'log_stok' => $logStok,
            'produk' => $this->produkModel->findAll(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'selected_produk' => $produkId
        ];

        return view('layout/header', $data)
             . view('layout/sidebar')
             . view('admin/laporan/stok_log', $data)
             . view('layout/footer');
    }
}