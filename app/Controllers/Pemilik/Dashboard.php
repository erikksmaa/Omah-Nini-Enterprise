<?php

namespace App\Controllers\Pemilik;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\SupplierModel;
use App\Models\MotifModel;
use App\Models\WarnaModel;
use App\Models\UserModel;
use App\Models\PelangganModel;
use App\Models\TransaksiModel;
use App\Models\PembelianModel;

class Dashboard extends BaseController
{
    protected $produkModel;
    protected $supplierModel;
    protected $motifModel;
    protected $warnaModel;
    protected $userModel;
    protected $pelangganModel;
    protected $transaksiModel;
    protected $pembelianModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->supplierModel = new SupplierModel();
        $this->motifModel = new MotifModel();
        $this->warnaModel = new WarnaModel();
        $this->userModel = new UserModel();
        $this->pelangganModel = new PelangganModel();
        $this->transaksiModel = new TransaksiModel();
        $this->pembelianModel = new PembelianModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard Owner',
            'total_produk' => $this->produkModel->countAllResults(),
            'total_supplier' => $this->supplierModel->countAllResults(),
            'total_motif' => $this->motifModel->countAllResults(),
            'total_warna' => $this->warnaModel->countAllResults(),
            'total_user' => $this->userModel->countAllResults(),
            'total_pelanggan' => $this->pelangganModel->countAllResults(),
            'stok_menipis' => $this->produkModel->getLowStockProducts(10),
            'transaksi_hari_ini' => $this->transaksiModel->getCountTransactionsToday(),
            'pembelian_bulan_ini' => $this->pembelianModel->getCountPembelianBulanIni(),
            'penjualan_bulan_ini' => $this->transaksiModel->getCountTransactionsThisMonth(),
        ];
        return view('pemilik/dashboard/index', $data);
    }
}