<?php

namespace App\Controllers\Karyawan;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\PembelianModel;
use App\Models\TransaksiModel;

class Dashboard extends BaseController
{
    protected $produkModel;
    protected $pembelianModel;
    protected $transaksiModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->pembelianModel = new PembelianModel();
        $this->transaksiModel = new TransaksiModel();
    }

    public function index()
    {
        // Data stok menipis (dari model sudah lengkap dengan nama_motif, nama_warna, nama_supplier)
        $stokMenipis = $this->produkModel->getLowStockProducts(10);
        
        // Format data stok menipis
        $stokMenipisData = [];
        foreach ($stokMenipis as $item) {
            // Gunakan data yang sudah tersedia dari query join
            $nama_produk = ($item['nama_motif'] ?? '?') . ' - ' . ($item['nama_warna'] ?? '?');
            $stokMenipisData[] = [
                'id'           => $item['id'],
                'nama_produk'  => $nama_produk,
                'sku'          => $item['sku'] ?? '',
                'stok'         => $item['stok'],
                'min_stok'     => $item['min_stok'],
                'nama_supplier'=> $item['nama_supplier'] ?? '',
            ];
        }

        $data = [
            'title'                 => 'Dashboard Karyawan',
            'role'                  => session()->get('role'),
            'username'              => session()->get('username'),
            'total_produk'          => $this->produkModel->getTotalProduk(),
            'total_stok'            => $this->produkModel->getTotalStockQuantity(),
            'stok_menipis'          => $stokMenipisData,
            'pembelian_bulan_ini'   => $this->pembelianModel->getCountPembelianBulanIni(),
            'penjualan_bulan_ini'   => $this->transaksiModel->getCountTransactionsThisMonth(),
            'transaksi_hari_ini'    => $this->transaksiModel->getCountTransactionsToday(),
        ];

        return view('karyawan/dashboard/index', $data);
    }
}