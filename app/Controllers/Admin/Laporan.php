<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\PembelianModel;
use App\Models\KeuanganModel;
use App\Models\LogStokModel;
use App\Models\ProdukModel;
use App\Models\DetailTransaksiModel;

class Laporan extends BaseController
{
    protected $transaksiModel;
    protected $pembelianModel;
    protected $keuanganModel;
    protected $logStokModel;
    protected $produkModel;
    protected $detailTransaksiModel;
    protected $db;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }

        if (session()->get('role') != 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $this->db = \Config\Database::connect();
        $this->transaksiModel = new TransaksiModel();
        $this->pembelianModel = new PembelianModel();
        $this->keuanganModel = new KeuanganModel();
        $this->logStokModel = new LogStokModel();
        $this->produkModel = new ProdukModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
    }

    // Dashboard Laporan
    public function index()
    {
        $data = [
            'title' => 'Dashboard Laporan'
        ];
        return view('admin/laporan/index', $data);
    }

    // Laporan Keuangan
    public function keuangan()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Data pemasukan
        $pemasukan = $this->keuanganModel
            ->where('tipe', 'pemasukan')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->selectSum('jumlah')
            ->first();

        // Data pengeluaran
        $pengeluaran = $this->keuanganModel
            ->where('tipe', 'pengeluaran')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->selectSum('jumlah')
            ->first();

        // Data per kategori
        $pemasukanByKategori = $this->keuanganModel
            ->select('kategori, SUM(jumlah) as total')
            ->where('tipe', 'pemasukan')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->groupBy('kategori')
            ->findAll();

        $pengeluaranByKategori = $this->keuanganModel
            ->select('kategori, SUM(jumlah) as total')
            ->where('tipe', 'pengeluaran')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->groupBy('kategori')
            ->findAll();

        // Detail transaksi keuangan
        $detail = $this->keuanganModel
            ->select('keuangan.*, users.username')
            ->join('users', 'users.user_id = keuangan.id_user', 'left')
            ->where('MONTH(keuangan.tanggal_transaksi)', $bulan)
            ->where('YEAR(keuangan.tanggal_transaksi)', $tahun)
            ->orderBy('keuangan.tanggal_transaksi', 'DESC')
            ->findAll();

        $totalPemasukan = $pemasukan['jumlah'] ?? 0;
        $totalPengeluaran = $pengeluaran['jumlah'] ?? 0;
        $labaRugi = $totalPemasukan - $totalPengeluaran;

        $data = [
            'title' => 'Laporan Keuangan',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_pemasukan' => $totalPemasukan,
            'total_pengeluaran' => $totalPengeluaran,
            'laba_rugi' => $labaRugi,
            'pemasukan_by_kategori' => $pemasukanByKategori,
            'pengeluaran_by_kategori' => $pengeluaranByKategori,
            'detail' => $detail,
            'bulan_list' => $this->getBulanList(),
            'tahun_list' => $this->getTahunList()
        ];

        return view('admin/laporan/keuangan', $data);
    }

    // Laporan Penjualan
    public function penjualan()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');

        // Debug: Log query untuk cek
        log_message('debug', 'Start date: ' . $start_date);
        log_message('debug', 'End date: ' . $end_date);

        // Data penjualan - cek apakah ada data
        $penjualan = $this->transaksiModel
            ->select('transaksi.*, users.username')
            ->join('users', 'users.user_id = transaksi.id_user', 'left')
            ->where('transaksi.status', 'selesai')
            ->where('transaksi.created_at >=', $start_date . ' 00:00:00')
            ->where('transaksi.created_at <=', $end_date . ' 23:59:59')
            ->orderBy('transaksi.created_at', 'DESC')
            ->findAll();

        // Debug: Cek jumlah data
        log_message('debug', 'Jumlah penjualan: ' . count($penjualan));

        // Statistik
        $totalTransaksi = count($penjualan);
        $totalOmset = array_sum(array_column($penjualan, 'total_bayar'));

        // Total item terjual
        $totalItemTerjual = $this->db->table('detail_transaksi')
            ->select('SUM(detail_transaksi.jumlah) as total')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('transaksi.created_at >=', $start_date . ' 00:00:00')
            ->where('transaksi.created_at <=', $end_date . ' 23:59:59')
            ->get()
            ->getRow()
            ->total ?? 0;

        // Penjualan per hari
        $penjualanPerHari = $this->db->table('transaksi')
            ->select('DATE(created_at) as tanggal, COUNT(*) as jumlah_transaksi, SUM(total_bayar) as total')
            ->where('status', 'selesai')
            ->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->groupBy('DATE(created_at)')
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();

        // Produk terlaris
        $produkTerlaris = $this->db->table('detail_transaksi')
            ->select('detail_transaksi.id_produk, detail_transaksi.nama_produk, SUM(detail_transaksi.jumlah) as total_terjual, SUM(detail_transaksi.subtotal) as total_omset')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('transaksi.created_at >=', $start_date . ' 00:00:00')
            ->where('transaksi.created_at <=', $end_date . ' 23:59:59')
            ->groupBy('detail_transaksi.id_produk, detail_transaksi.nama_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Laporan Penjualan',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'penjualan' => $penjualan,
            'total_transaksi' => $totalTransaksi,
            'total_omset' => $totalOmset,
            'total_item_terjual' => $totalItemTerjual,
            'penjualan_per_hari' => $penjualanPerHari,
            'produk_terlaris' => $produkTerlaris
        ];

        return view('admin/laporan/penjualan', $data);
    }
    // Laporan Pembelian
    public function pembelian()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');

        // Data pembelian
        $pembelian = $this->pembelianModel
            ->select('pembelian.*, supplier.nama as supplier_nama, users.username')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->join('users', 'users.user_id = pembelian.id_user', 'left')
            ->where('pembelian.tanggal_pembelian >=', $start_date)
            ->where('pembelian.tanggal_pembelian <=', $end_date)
            ->orderBy('pembelian.tanggal_pembelian', 'DESC')
            ->findAll();

        // Statistik
        $totalTransaksi = count($pembelian);
        $totalPengeluaran = array_sum(array_column($pembelian, 'total_harga'));

        // Pembelian per supplier
        $pembelianPerSupplier = $this->db->table('pembelian')
            ->select('supplier.nama as supplier_nama, COUNT(*) as jumlah_transaksi, SUM(pembelian.total_harga) as total')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->where('pembelian.tanggal_pembelian >=', $start_date)
            ->where('pembelian.tanggal_pembelian <=', $end_date)
            ->groupBy('pembelian.id_supplier')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Laporan Pembelian',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'pembelian' => $pembelian,
            'total_transaksi' => $totalTransaksi,
            'total_pengeluaran' => $totalPengeluaran,
            'pembelian_per_supplier' => $pembelianPerSupplier
        ];

        return view('admin/laporan/pembelian', $data);
    }

    // Laporan Laba/Rugi
    public function labaRugi()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $start_date = $tahun . '-' . $bulan . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        // Total penjualan (omset)
        $totalPenjualan = $this->transaksiModel
            ->where('status', 'selesai')
            ->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->selectSum('total_bayar')
            ->first()['total_bayar'] ?? 0;

        // HPP (Harga Pokok Penjualan) - PERBAIKAN tanpa GROUP BY
        $hpp = $this->db->table('detail_transaksi')
            ->select('SUM(detail_transaksi.jumlah * produk.harga_beli) as total_hpp')
            ->join('produk', 'produk.id = detail_transaksi.id_produk')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->where('transaksi.created_at >=', $start_date . ' 00:00:00')
            ->where('transaksi.created_at <=', $end_date . ' 23:59:59')
            ->get()
            ->getRow()
            ->total_hpp ?? 0;

        // Biaya operasional (pengeluaran non-pembelian)
        $biayaOperasional = $this->keuanganModel
            ->where('tipe', 'pengeluaran')
            ->where('kategori !=', 'pembelian')
            ->where('MONTH(tanggal_transaksi)', $bulan)
            ->where('YEAR(tanggal_transaksi)', $tahun)
            ->selectSum('jumlah')
            ->first()['jumlah'] ?? 0;

        // Total pembelian barang
        $totalPembelian = $this->pembelianModel
            ->where('MONTH(tanggal_pembelian)', $bulan)
            ->where('YEAR(tanggal_pembelian)', $tahun)
            ->selectSum('total_harga')
            ->first()['total_harga'] ?? 0;

        $labaKotor = $totalPenjualan - $hpp;
        $labaBersih = $labaKotor - $biayaOperasional;
        $marginLaba = $totalPenjualan > 0 ? ($labaBersih / $totalPenjualan) * 100 : 0;

        $data = [
            'title' => 'Laporan Laba/Rugi',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_penjualan' => $totalPenjualan,
            'hpp' => $hpp,
            'laba_kotor' => $labaKotor,
            'total_pembelian' => $totalPembelian,
            'biaya_operasional' => $biayaOperasional,
            'laba_bersih' => $labaBersih,
            'margin_laba' => $marginLaba,
            'bulan_list' => $this->getBulanList(),
            'tahun_list' => $this->getTahunList()
        ];

        return view('admin/laporan/laba_rugi', $data);
    }

    // Audit Log Stok
    public function logStok()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');
        $produk_id = $this->request->getGet('produk_id');
        $tipe = $this->request->getGet('tipe');

        $builder = $this->logStokModel
            ->select('log_stok.*, produk.nama_barang, produk.sku, users.username')
            ->join('produk', 'produk.id = log_stok.id_produk')
            ->join('users', 'users.user_id = log_stok.id_user', 'left')
            ->where('log_stok.created_at >=', $start_date . ' 00:00:00')
            ->where('log_stok.created_at <=', $end_date . ' 23:59:59');

        if ($produk_id) {
            $builder->where('log_stok.id_produk', $produk_id);
        }

        if ($tipe) {
            $builder->where('log_stok.tipe_ref', $tipe);
        }

        $log = $builder->orderBy('log_stok.created_at', 'DESC')->paginate(50);
        $pager = $this->logStokModel->pager;

        // Statistik
        $totalMasuk = $this->logStokModel
            ->where('tipe_ref', 'pembelian')
            ->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->selectSum('jumlah_perubahan')
            ->first()['jumlah_perubahan'] ?? 0;

        $totalKeluar = $this->logStokModel
            ->where('tipe_ref', 'penjualan')
            ->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->selectSum('jumlah_perubahan')
            ->first()['jumlah_perubahan'] ?? 0;

        $data = [
            'title' => 'Audit Log Stok',
            'log' => $log,
            'pager' => $pager,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'produk_id' => $produk_id,
            'tipe' => $tipe,
            'produk_list' => $this->produkModel->findAll(),
            'total_masuk' => abs($totalMasuk),
            'total_keluar' => abs($totalKeluar)
        ];

        return view('admin/laporan/log_stok', $data);
    }

    // Laporan Produk
    public function produk()
    {
        // Produk dengan stok terbanyak (limit 10)
        $stokTerbanyak = $this->produkModel
            ->select('produk.*, kategori.nama as kategori_nama')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->orderBy('stok', 'DESC')
            ->limit(10)
            ->findAll();

        // Produk dengan stok menipis (dengan pagination)
        $stokMenipis = $this->produkModel
            ->select('produk.*, kategori.nama as kategori_nama')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->where('stok <=', 'min_stok', false)
            ->orderBy('stok', 'ASC')
            ->paginate(10);

        $pagerStokMenipis = $this->produkModel->pager;

        // Produk terlaris all time (limit 10)
        $produkTerlaris = $this->db->table('detail_transaksi')
            ->select('detail_transaksi.id_produk, detail_transaksi.nama_produk, SUM(detail_transaksi.jumlah) as total_terjual, SUM(detail_transaksi.subtotal) as total_omset')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->groupBy('detail_transaksi.id_produk, detail_transaksi.nama_produk')
            ->orderBy('total_terjual', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // PERBAIKAN: Produk tidak pernah terjual dengan pagination
        // Ambil ID produk yang sudah terjual
        $terjual = $this->db->table('detail_transaksi')
            ->select('id_produk')
            ->groupBy('id_produk')
            ->get()
            ->getResultArray();

        $terjualIds = array_column($terjual, 'id_produk');

        // Gunakan produkModel dengan pagination
        $builder = $this->produkModel
            ->select('produk.id, produk.nama_barang, produk.sku, produk.stok, produk.harga_beli')
            ->orderBy('produk.nama_barang', 'ASC');

        if (!empty($terjualIds)) {
            $builder->whereNotIn('produk.id', $terjualIds);
        }

        $produkNeverSold = $builder->paginate(10);
        $pagerNeverSold = $this->produkModel->pager;

        // Total nilai stok
        $totalNilaiStok = $this->db->table('produk')
            ->select('SUM(stok * harga_beli) as total')
            ->get()
            ->getRow()
            ->total ?? 0;

        $data = [
            'title' => 'Laporan Produk',
            'stok_terbanyak' => $stokTerbanyak,
            'stok_menipis' => $stokMenipis,
            'pager_stok_menipis' => $pagerStokMenipis,
            'produk_terlaris' => $produkTerlaris,
            'produk_never_sold' => $produkNeverSold,
            'pager_never_sold' => $pagerNeverSold,
            'total_nilai_stok' => $totalNilaiStok,
            'total_produk' => $this->produkModel->countAll()
        ];

        return view('admin/laporan/produk', $data);
    }

    // Export Excel (opsional)
    public function exportPenjualan()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');

        $penjualan = $this->transaksiModel
            ->select('transaksi.*, users.username')
            ->join('users', 'users.user_id = transaksi.id_user', 'left')
            ->where('transaksi.status', 'selesai')
            ->where('transaksi.created_at >=', $start_date . ' 00:00:00')
            ->where('transaksi.created_at <=', $end_date . ' 23:59:59')
            ->orderBy('transaksi.created_at', 'DESC')
            ->findAll();

        // Load library Excel (pastikan sudah install)
        // return $this->response->download('laporan_penjualan.xlsx', $data);

        return redirect()->back()->with('info', 'Fitur export sedang dalam pengembangan');
    }

    // Helper functions
    private function getBulanList()
    {
        return [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
    }

    private function getTahunList()
    {
        $tahun = [];
        for ($i = 2023; $i <= date('Y'); $i++) {
            $tahun[$i] = $i;
        }
        return $tahun;
    }
}