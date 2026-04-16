<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KeuanganModel;
use App\Models\LogStokModel;
use App\Models\TransaksiModel;
use App\Models\PembelianModel;

class Laporan extends BaseController
{
    protected $keuanganModel;
    protected $logStokModel;
    protected $transaksiModel;
    protected $pembelianModel;

    public function __construct()
    {
        $this->keuanganModel = new KeuanganModel();
        $this->logStokModel = new LogStokModel();
        $this->transaksiModel = new TransaksiModel();
        $this->pembelianModel = new PembelianModel();
    }

    public function keuangan()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Pemasukan
        $pemasukan = $this->keuanganModel->where('tipe', 'pemasukan')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->selectSum('jumlah')
            ->first();

        // Pengeluaran
        $pengeluaran = $this->keuanganModel->where('tipe', 'pengeluaran')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->selectSum('jumlah')
            ->first();

        $totalPemasukan = $pemasukan['jumlah'] ?? 0;
        $totalPengeluaran = $pengeluaran['jumlah'] ?? 0;
        $labaRugi = $totalPemasukan - $totalPengeluaran;

        $data = [
            'title' => 'Laporan Keuangan',
            'pemasukan' => $totalPemasukan,
            'pengeluaran' => $totalPengeluaran,
            'laba_rugi' => $labaRugi,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'detail' => $this->keuanganModel
                ->where('MONTH(tanggal_transaksi)', $bulan)
                ->where('YEAR(tanggal_transaksi)', $tahun)
                ->orderBy('tanggal_transaksi', 'DESC')
                ->findAll()
        ];
        return view('admin/laporan/keuangan', $data);
    }

    public function logStok()
    {
        $data = [
            'title' => 'Log Histori Stok',
            'log' => $this->logStokModel->select('log_stok.*, produk.nama_barang, produk.sku')
                ->join('produk', 'produk.id = log_stok.id_produk')
                ->orderBy('log_stok.created_at', 'DESC')
                ->findAll()
        ];
        return view('admin/laporan/log_stok', $data);
    }

    public function labaRugi()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Hitung total penjualan
        $totalPenjualan = $this->transaksiModel
            ->where('MONTH(created_at)', $bulan)
            ->where('YEAR(created_at)', $tahun)
            ->selectSum('total_bayar')
            ->first()['total_bayar'] ?? 0;

        // Hitung total pembelian (HPP)
        $totalPembelian = $this->pembelianModel
            ->where('MONTH(tanggal_pembelian)', $bulan)
            ->where('YEAR(tanggal_pembelian)', $tahun)
            ->selectSum('total_harga')
            ->first()['total_harga'] ?? 0;

        // Hitung biaya operasional (jika ada)
        $biayaOperasional = $this->keuanganModel
            ->where('tipe', 'pengeluaran')
            ->where('kategori', 'operasional')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->selectSum('jumlah')
            ->first()['jumlah'] ?? 0;

        $labaKotor = $totalPenjualan - $totalPembelian;
        $labaBersih = $labaKotor - $biayaOperasional;

        $data = [
            'title' => 'Laporan Laba/Rugi',
            'total_penjualan' => $totalPenjualan,
            'total_pembelian' => $totalPembelian,
            'biaya_operasional' => $biayaOperasional,
            'laba_kotor' => $labaKotor,
            'laba_bersih' => $labaBersih,
            'bulan' => $bulan,
            'tahun' => $tahun
        ];
        return view('admin/laporan/laba_rugi', $data);
    }
}