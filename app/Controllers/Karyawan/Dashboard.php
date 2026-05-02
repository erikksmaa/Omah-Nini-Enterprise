<?php

namespace App\Controllers\Karyawan;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\PembelianModel;
use App\Models\TransaksiModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $produkModel = new ProdukModel();
        $pembelianModel = new PembelianModel();
        $transaksiModel = new TransaksiModel();

        // Data stok menipis
        $stokMenipis = $produkModel->getLowStockProducts(10);
        $stokMenipisData = [];
        foreach ($stokMenipis as $item) {
            // Jika model getLowStockProducts() sudah mengembalikan nama_motif, nama_warna, supplier, maka langsung pakai
            $nama_produk = $item['nama_produk'] ?? ($item['sku'] . ' - ' . ($item['nama_motif'] ?? '?') . ' ' . ($item['nama_warna'] ?? '?'));
            $stokMenipisData[] = [
                'id' => $item['id'],
                'nama_produk' => $nama_produk,
                'sku' => $item['sku'] ?? '',
                'stok' => $item['stok'],
                'min_stok' => $item['min_stok'],
            ];
        }

        // Statistik ringkas
        $totalProduk = $produkModel->countAllResults();
        $totalStok = $produkModel->getTotalStockQuantity();
        $pembelianBulanIni = $pembelianModel->getCountPembelianBulanIni();
        $penjualanBulanIni = $transaksiModel->getCountTransactionsThisMonth();
        $transaksiHariIni = $transaksiModel->getCountTransactionsToday();

        $data = [
            'title'         => 'Dashboard Karyawan',
            'role'          => session()->get('role'),
            'username'      => session()->get('username'),
            'total_produk'  => $totalProduk,
            'total_stok'    => $totalStok,
            'stok_menipis'  => $stokMenipisData,
            'pembelian_bulan_ini' => $pembelianBulanIni,
            'penjualan_bulan_ini' => $penjualanBulanIni,
            'transaksi_hari_ini'  => $transaksiHariIni,
        ];

        return view('karyawan/dashboard/index', $data);
    }
}