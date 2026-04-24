<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\PembelianModel;
use App\Models\KeuanganModel;
use App\Models\LogStokModel;
use App\Models\ProdukModel;
use App\Models\LaporanModel;

class Laporan extends BaseController
{
    protected $transaksiModel;
    protected $pembelianModel;
    protected $keuanganModel;
    protected $logStokModel;
    protected $produkModel;
    protected $laporanModel;
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
        $this->laporanModel = new LaporanModel();
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

        $totalPemasukan = $this->laporanModel->getTotalPemasukan($bulan, $tahun);
        $totalPengeluaran = $this->laporanModel->getTotalPengeluaran($bulan, $tahun);
        
        $data = [
            'title' => 'Laporan Keuangan',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_pemasukan' => $totalPemasukan,
            'total_pengeluaran' => $totalPengeluaran,
            'laba_rugi' => $totalPemasukan - $totalPengeluaran,
            'pemasukan_by_kategori' => $this->laporanModel->getPemasukanByKategori($bulan, $tahun),
            'pengeluaran_by_kategori' => $this->laporanModel->getPengeluaranByKategori($bulan, $tahun),
            'detail' => $this->laporanModel->getDetailKeuangan($bulan, $tahun),
            'bulan_list' => $this->laporanModel->getBulanList(),
            'tahun_list' => $this->laporanModel->getTahunList()
        ];

        return view('admin/laporan/keuangan', $data);
    }

    // Laporan Penjualan
    public function penjualan()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');

        $penjualan = $this->laporanModel->getPenjualanByDate($start_date, $end_date);
        
        $data = [
            'title' => 'Laporan Penjualan',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'penjualan' => $penjualan,
            'total_transaksi' => count($penjualan),
            'total_omset' => array_sum(array_column($penjualan, 'total_bayar')),
            'total_item_terjual' => $this->laporanModel->getTotalItemTerjual($start_date, $end_date),
            'penjualan_per_hari' => $this->laporanModel->getPenjualanPerHari($start_date, $end_date),
            'produk_terlaris' => $this->laporanModel->getProdukTerlaris($start_date, $end_date, 10)
        ];

        return view('admin/laporan/penjualan', $data);
    }

    // Laporan Pembelian
    public function pembelian()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');

        $pembelian = $this->laporanModel->getPembelianByDate($start_date, $end_date);
        
        $data = [
            'title' => 'Laporan Pembelian',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'pembelian' => $pembelian,
            'total_transaksi' => count($pembelian),
            'total_pengeluaran' => array_sum(array_column($pembelian, 'total_harga')),
            'pembelian_per_supplier' => $this->laporanModel->getPembelianPerSupplier($start_date, $end_date)
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

        $totalPenjualan = $this->laporanModel->getTotalPenjualanForLabaRugi($start_date, $end_date);
        $hpp = $this->laporanModel->getHpp($start_date, $end_date);
        $biayaOperasional = $this->laporanModel->getBiayaOperasional($bulan, $tahun);
        $totalPembelian = $this->laporanModel->getTotalPembelianForLabaRugi($bulan, $tahun);

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
            'bulan_list' => $this->laporanModel->getBulanList(),
            'tahun_list' => $this->laporanModel->getTahunList()
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

    // Gunakan method getFiltered dari model
    $log = $this->logStokModel->getFiltered($start_date, $end_date, $produk_id, $tipe, 50);
    $pager = $this->logStokModel->pager;

    // Statistik mutasi stok
    $mutasi = $this->logStokModel->getMutasiStok($start_date, $end_date);

    $data = [
        'title' => 'Audit Log Stok',
        'log' => $log,
        'pager' => $pager,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'produk_id' => $produk_id,
        'tipe' => $tipe,
        'produk_list' => $this->produkModel->findAll(),
        'total_masuk' => $mutasi['masuk'],
        'total_keluar' => $mutasi['keluar']
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

    // Produk tidak pernah terjual dengan pagination
    $terjual = $this->db->table('detail_transaksi')
        ->select('id_produk')
        ->groupBy('id_produk')
        ->get()
        ->getResultArray();

    $terjualIds = array_column($terjual, 'id_produk');

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
        'pager_stok_menipis' => $pagerStokMenipis,  // ← PASTIKAN INI ADA
        'produk_terlaris' => $produkTerlaris,
        'produk_never_sold' => $produkNeverSold,
        'pager_never_sold' => $pagerNeverSold,     // ← PASTIKAN INI ADA
        'total_nilai_stok' => $totalNilaiStok,
        'total_produk' => $this->produkModel->countAll()
    ];

    return view('admin/laporan/produk', $data);
}

    // ========== EXPORT METHODS ==========
    
    public function exportPenjualan()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');

        $penjualan = $this->laporanModel->getPenjualanByDate($start_date, $end_date);

        $headers = ['No', 'No Invoice', 'Tanggal', 'Total Bayar', 'Tipe Pembayaran', 'Kasir', 'Catatan'];
        $data = [];
        $no = 1;

        foreach ($penjualan as $item) {
            $data[] = [
                $no++,
                $item['no_invoice'],
                date('d-m-Y H:i:s', strtotime($item['created_at'])),
                $item['total_bayar'],
                strtoupper($item['tipe_pembayaran']),
                $item['username'] ?? '-',
                $item['catatan'] ?? '-'
            ];
        }

        $additionalInfo = [
            'Periode: ' . date('d/m/Y', strtotime($start_date)) . ' s/d ' . date('d/m/Y', strtotime($end_date)),
            'Total Transaksi: ' . count($penjualan) . ' transaksi',
            'Total Omset: Rp ' . number_format(array_sum(array_column($penjualan, 'total_bayar')), 0, ',', '.'),
            'Tanggal Export: ' . date('d/m/Y H:i:s')
        ];

        exportToExcel($data, $headers, 'LAPORAN PENJUALAN', 'Laporan_Penjualan', null, $additionalInfo);
    }

    public function exportPembelian()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');

        $pembelian = $this->laporanModel->getPembelianByDate($start_date, $end_date);

        $headers = ['No', 'No Invoice', 'Supplier', 'Tanggal', 'Total Harga', 'User', 'Catatan'];
        $data = [];
        $no = 1;

        foreach ($pembelian as $item) {
            $data[] = [
                $no++,
                $item['no_invoice'],
                $item['supplier_nama'],
                date('d-m-Y', strtotime($item['tanggal_pembelian'])),
                $item['total_harga'],
                $item['username'] ?? '-',
                $item['catatan'] ?? '-'
            ];
        }

        $additionalInfo = [
            'Periode: ' . date('d/m/Y', strtotime($start_date)) . ' s/d ' . date('d/m/Y', strtotime($end_date)),
            'Total Transaksi: ' . count($pembelian) . ' transaksi',
            'Total Pengeluaran: Rp ' . number_format(array_sum(array_column($pembelian, 'total_harga')), 0, ',', '.'),
            'Tanggal Export: ' . date('d/m/Y H:i:s')
        ];

        exportToExcel($data, $headers, 'LAPORAN PEMBELIAN', 'Laporan_Pembelian', null, $additionalInfo);
    }

    public function exportKeuangan()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $keuangan = $this->laporanModel->getDetailKeuangan($bulan, $tahun);
        $totalPemasukan = $this->laporanModel->getTotalPemasukan($bulan, $tahun);
        $totalPengeluaran = $this->laporanModel->getTotalPengeluaran($bulan, $tahun);

        $headers = ['No', 'Tanggal', 'Tipe', 'Kategori', 'Referensi', 'Jumlah', 'User'];
        $data = [];
        $no = 1;

        foreach ($keuangan as $item) {
            $data[] = [
                $no++,
                date('d-m-Y', strtotime($item['tanggal_transaksi'])),
                $item['tipe'] == 'pemasukan' ? 'PEMASUKAN' : 'PENGELUARAN',
                ucfirst($item['kategori']),
                $item['tipe_ref'] . ' #' . $item['id_ref'],
                $item['jumlah'],
                $item['username'] ?? '-'
            ];
        }

        $bulanNames = $this->laporanModel->getBulanList();

        $additionalInfo = [
            'Periode: ' . $bulanNames[$bulan] . ' ' . $tahun,
            'Total Pemasukan: Rp ' . number_format($totalPemasukan, 0, ',', '.'),
            'Total Pengeluaran: Rp ' . number_format($totalPengeluaran, 0, ',', '.'),
            'Laba/Rugi: Rp ' . number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.'),
            'Tanggal Export: ' . date('d/m/Y H:i:s')
        ];

        exportToExcel($data, $headers, 'LAPORAN KEUANGAN', 'Laporan_Keuangan', null, $additionalInfo);
    }

    public function exportLabaRugi()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $start_date = $tahun . '-' . $bulan . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $totalPenjualan = $this->laporanModel->getTotalPenjualanForLabaRugi($start_date, $end_date);
        $hpp = $this->laporanModel->getHpp($start_date, $end_date);
        $biayaOperasional = $this->laporanModel->getBiayaOperasional($bulan, $tahun);

        $labaKotor = $totalPenjualan - $hpp;
        $labaBersih = $labaKotor - $biayaOperasional;
        $marginLaba = $totalPenjualan > 0 ? ($labaBersih / $totalPenjualan) * 100 : 0;

        $detailPenjualan = $this->laporanModel->getPenjualanByDate($start_date, $end_date);

        $headers = ['No', 'No Invoice', 'Tanggal', 'Total Bayar'];
        $data = [];
        $no = 1;

        foreach ($detailPenjualan as $item) {
            $data[] = [
                $no++,
                $item['no_invoice'],
                date('d-m-Y', strtotime($item['created_at'])),
                $item['total_bayar']
            ];
        }

        $bulanNames = $this->laporanModel->getBulanList();

        $additionalInfo = [
            'Periode: ' . $bulanNames[$bulan] . ' ' . $tahun,
            '',
            'RINGKASAN LABA/RUGI:',
            'Total Penjualan (Omset): Rp ' . number_format($totalPenjualan, 0, ',', '.'),
            'HPP (Harga Pokok Penjualan): Rp ' . number_format($hpp, 0, ',', '.'),
            'Laba Kotor: Rp ' . number_format($labaKotor, 0, ',', '.'),
            'Biaya Operasional: Rp ' . number_format($biayaOperasional, 0, ',', '.'),
            'Laba Bersih: Rp ' . number_format($labaBersih, 0, ',', '.'),
            'Margin Laba: ' . number_format($marginLaba, 2) . '%',
            '',
            'Status: ' . ($labaBersih >= 0 ? 'UNTUNG' : 'RUGI')
        ];

        exportToExcel($data, $headers, 'LAPORAN LABA/RUGI', 'Laporan_Laba_Rugi', null, $additionalInfo);
    }

    public function exportStok()
    {
        $produk = $this->produkModel
            ->select('produk.*, kategori.nama as kategori_nama, supplier.nama as supplier_nama')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->join('supplier', 'supplier.id = produk.id_supplier', 'left')
            ->orderBy('produk.id', 'ASC')
            ->findAll();

        $headers = ['No', 'SKU', 'Nama Produk', 'Kategori', 'Supplier', 'Harga Beli', 'Harga Jual', 'Stok', 'Min Stok', 'Nilai Stok', 'Status'];
        $data = [];
        $no = 1;
        $totalNilaiStok = 0;

        foreach ($produk as $item) {
            $status = 'AMAN';
            if ($item['stok'] <= 0) {
                $status = 'HABIS';
            } elseif ($item['stok'] <= $item['min_stok']) {
                $status = 'MENIPIS';
            }

            $nilaiStok = $item['stok'] * $item['harga_beli'];
            $totalNilaiStok += $nilaiStok;

            $data[] = [
                $no++,
                $item['sku'],
                $item['nama_barang'],
                $item['kategori_nama'] ?? '-',
                $item['supplier_nama'] ?? '-',
                $item['harga_beli'],
                $item['harga_jual'],
                $item['stok'],
                $item['min_stok'],
                $nilaiStok,
                $status
            ];
        }

        $stokMenipis = $this->produkModel->where('stok <=', 'min_stok', false)->countAllResults();
        $stokHabis = $this->produkModel->where('stok', 0)->countAllResults();
        $stokAman = $this->produkModel->where('stok >', 'min_stok', false)->countAllResults();

        $additionalInfo = [
            'Tanggal Export: ' . date('d/m/Y H:i:s'),
            '',
            'RINGKASAN STOK:',
            'Total Produk: ' . count($produk) . ' produk',
            'Total Nilai Stok: Rp ' . number_format($totalNilaiStok, 0, ',', '.'),
            'Stok Aman: ' . $stokAman . ' produk',
            'Stok Menipis: ' . $stokMenipis . ' produk',
            'Stok Habis: ' . $stokHabis . ' produk'
        ];

        exportToExcel($data, $headers, 'LAPORAN STOK PRODUK', 'Laporan_Stok', null, $additionalInfo);
    }
}