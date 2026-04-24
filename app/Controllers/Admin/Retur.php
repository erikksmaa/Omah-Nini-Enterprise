<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReturPenjualanModel;
use App\Models\DetailReturPenjualanModel;
use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\ProdukModel;
use App\Models\LogStokModel;
use App\Models\KeuanganModel;

class Retur extends BaseController
{
    protected $returModel;
    protected $detailReturModel;
    protected $transaksiModel;
    protected $detailTransaksiModel;
    protected $produkModel;
    protected $logStokModel;
    protected $keuanganModel;
    protected $db;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role != 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $this->db = \Config\Database::connect();
        $this->returModel = new ReturPenjualanModel();
        $this->detailReturModel = new DetailReturPenjualanModel();
        $this->transaksiModel = new TransaksiModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
        $this->produkModel = new ProdukModel();
        $this->logStokModel = new LogStokModel();
        $this->keuanganModel = new KeuanganModel();
    }

    // Halaman utama retur
    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $perPage = 10;

        $retur = $this->returModel->getWithTransaksi($perPage, $keyword, $start_date, $end_date);
        $pager = $this->returModel->pager;

        $data = [
            'title' => 'Retur Penjualan',
            'retur' => $retur,
            'pager' => $pager,
            'keyword' => $keyword,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
        return view('admin/retur/index', $data);
    }

    // Form retur baru
    public function create()
    {
        $data = [
            'title' => 'Tambah Retur Penjualan',
            'transaksi' => $this->returModel->getAvailableTransactionsForRetur(),
            'no_retur' => $this->returModel->generateNoRetur()
        ];
        return view('admin/retur/create', $data);
    }

    // Get detail transaksi via AJAX
    public function getDetailTransaksi($id)
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ]);
        }

        $detail = $this->detailTransaksiModel
            ->where('id_transaksi', $id)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'transaksi' => $transaksi,
            'detail' => $detail
        ]);
    }

    // Proses simpan retur
    public function store()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'id_transaksi' => 'required',
            'tanggal_retur' => 'required|valid_date',
            'alasan' => 'required',
            'items' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $items = json_decode($this->request->getPost('items'), true);

        if (empty($items) || !is_array($items)) {
            return redirect()->back()->with('error', 'Minimal 1 produk harus diretur');
        }

        $total_retur = 0;
        foreach ($items as $item) {
            $total_retur += $item['subtotal'];
        }

        $no_retur = $this->returModel->generateNoRetur();
        $now = date('Y-m-d H:i:s');

        $this->db->transStart();

        try {
            // 1. Insert ke tabel retur_penjualan
            $returData = [
                'no_retur' => $no_retur,
                'id_transaksi' => $this->request->getPost('id_transaksi'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'total_retur' => $total_retur,
                'alasan' => $this->request->getPost('alasan'),
                'id_user' => session()->get('user_id'),
                'created_at' => $now
            ];

            $this->returModel->insert($returData);
            $retur_id = $this->returModel->getInsertID();

            // 2. Insert detail retur, update stok, dan catat log
            foreach ($items as $item) {
                // Insert detail retur
                $detailData = [
                    'id_retur' => $retur_id,
                    'id_detail_transaksi' => $item['id_detail_transaksi'],
                    'id_produk' => $item['id_produk'],
                    'nama_produk' => $item['nama_produk'],
                    'jumlah' => $item['jumlah'],
                    'harga_jual' => $item['harga_jual'],
                    'subtotal' => $item['subtotal']
                ];
                $this->detailReturModel->insert($detailData);

                // Update stok produk (kembalikan stok)
                $produk = $this->produkModel->find($item['id_produk']);
                $stok_baru = $produk['stok'] + $item['jumlah'];
                $this->produkModel->update($item['id_produk'], ['stok' => $stok_baru]);

                // Catat log stok
                $this->logStokModel->insert([
                    'id_produk' => $item['id_produk'],
                    'id_user' => session()->get('user_id'),
                    'tipe_ref' => 'retur_penjualan',
                    'id_ref' => $retur_id,
                    'jumlah_sebelum' => $produk['stok'],
                    'jumlah_perubahan' => $item['jumlah'],
                    'jumlah_sesudah' => $stok_baru,
                    'aktivitas' => 'Retur penjualan - ' . $this->request->getPost('alasan'),
                    'created_at' => $now
                ]);
            }

            // 3. Catat keuangan (pengeluaran karena retur)
            $this->keuanganModel->insert([
                'id_user' => session()->get('user_id'),
                'tipe' => 'pengeluaran',
                'kategori' => 'retur_penjualan',
                'tipe_ref' => 'retur_penjualan',
                'id_ref' => $retur_id,
                'jumlah' => $total_retur,
                'tanggal_transaksi' => $this->request->getPost('tanggal_retur'),
                'created_at' => $now
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return redirect()->to('/admin/retur')->with('success', 'Retur penjualan berhasil disimpan');

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Retur error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // Detail retur
    public function detail($id)
    {
        $retur = $this->returModel->getById($id);
        if (!$retur) {
            return redirect()->to('/admin/retur')->with('error', 'Data tidak ditemukan');
        }

        $detail = $this->detailReturModel->getByRetur($id);

        $data = [
            'title' => 'Detail Retur Penjualan',
            'retur' => $retur,
            'detail' => $detail
        ];
        return view('admin/retur/detail', $data);
    }

    // Laporan Retur Penjualan
    public function laporan()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');
        $status = $this->request->getGet('status');

        $retur = $this->returModel->getReturReport($start_date, $end_date, $status);
        $statistik = $this->returModel->getReturStatistics($start_date, $end_date);

        $data = [
            'title' => 'Laporan Retur Penjualan',
            'retur' => $retur,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $status,
            'total_retur' => $statistik['total_retur'],
            'total_nominal_retur' => $statistik['total_nominal'],
            'rata_retur' => $statistik['rata_rata'],
            'top_produk_diretur' => $this->returModel->getTopProductsReturned(10),
            'retur_per_bulan' => $this->returModel->getReturPerMonth(),
            'alasan_terbanyak' => $this->returModel->getTopReasons(5)
        ];

        return view('admin/retur/laporan', $data);
    }

    // Export Excel Laporan Retur
    public function exportExcel()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');

        $retur = $this->returModel->getReturReport($start_date, $end_date);
        $detailRetur = $this->detailReturModel->getForExport($start_date, $end_date);

        $headers = ['No', 'No Retur', 'No Invoice', 'Tanggal Retur', 'Total Retur', 'Alasan', 'User'];
        $data = [];
        $no = 1;
        $totalNominal = 0;

        foreach ($retur as $item) {
            $totalNominal += $item['total_retur'];
            $data[] = [
                $no++,
                $item['no_retur'],
                $item['no_invoice'],
                date('d-m-Y', strtotime($item['tanggal_retur'])),
                $item['total_retur'],
                $item['alasan'],
                $item['username'] ?? '-'
            ];
        }

        $detailHeaders = ['No', 'No Retur', 'Nama Produk', 'Jumlah', 'Harga Jual', 'Subtotal'];
        $detailData = [];
        $noDetail = 1;

        foreach ($detailRetur as $item) {
            $detailData[] = [
                $noDetail++,
                $item['no_retur'],
                $item['nama_produk'],
                $item['jumlah'],
                $item['harga_jual'],
                $item['subtotal']
            ];
        }

        $additionalInfo = [
            'Periode: ' . date('d/m/Y', strtotime($start_date)) . ' s/d ' . date('d/m/Y', strtotime($end_date)),
            'Total Retur: ' . count($retur) . ' transaksi',
            'Total Nominal Retur: Rp ' . number_format($totalNominal, 0, ',', '.'),
            'Rata-rata Retur: Rp ' . number_format(count($retur) > 0 ? $totalNominal / count($retur) : 0, 0, ',', '.'),
            'Tanggal Export: ' . date('d/m/Y H:i:s')
        ];

        exportToExcelWithMultipleSheets($data, $headers, $detailData, $detailHeaders, 'LAPORAN RETUR PENJUALAN', 'Laporan_Retur', $additionalInfo);
    }
}