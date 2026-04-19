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
        $data = [
            'title' => 'Retur Penjualan',
            'retur' => $this->returModel->getWithTransaksi()
        ];
        return view('admin/retur/index', $data);
    }

    // Form retur baru
    public function create()
    {
        // Ambil transaksi yang sudah selesai dan belum diretur
        $transaksi = $this->db->table('transaksi')
            ->select('transaksi.*, users.username')
            ->join('users', 'users.user_id = transaksi.id_user', 'left')
            ->where('transaksi.status', 'selesai')
            ->whereNotIn('transaksi.id', function ($builder) {
                $builder->select('id_transaksi')->from('retur_penjualan');
            })
            ->orderBy('transaksi.id', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Tambah Retur Penjualan',
            'transaksi' => $transaksi,
            'no_retur' => $this->returModel->generateNoRetur()
        ];
        return view('admin/retur/create', $data);
    }

    // Get detail transaksi via AJAX
    // app/Controllers/Admin/Retur.php
    public function getDetailTransaksi($id)
    {
        // Set header untuk JSON response
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

        // Debug: log data
        log_message('debug', 'Transaksi ID: ' . $id);
        log_message('debug', 'Detail ditemukan: ' . count($detail));

        return $this->response->setJSON([
            'success' => true,
            'transaksi' => $transaksi,
            'detail' => $detail
        ]);
    }

    // Proses simpan retur
    // app/Controllers/Admin/Retur.php
    public function store()
    {
        // Validasi
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

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Insert ke tabel retur_penjualan (Gunakan Query Builder langsung)
            $returData = [
                'no_retur' => $no_retur,
                'id_transaksi' => $this->request->getPost('id_transaksi'),
                'tanggal_retur' => $this->request->getPost('tanggal_retur'),
                'total_retur' => $total_retur,
                'alasan' => $this->request->getPost('alasan'),
                'id_user' => session()->get('user_id'),
                'created_at' => $now
            ];

            $db->table('retur_penjualan')->insert($returData);
            $retur_id = $db->insertID();

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
                $db->table('detail_retur_penjualan')->insert($detailData);

                // Update stok produk (kembalikan stok)
                $produk = $db->table('produk')->where('id', $item['id_produk'])->get()->getRowArray();
                $stok_baru = $produk['stok'] + $item['jumlah'];
                $db->table('produk')->where('id', $item['id_produk'])->update(['stok' => $stok_baru]);

                // Catat log stok
                $db->table('log_stok')->insert([
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
            $db->table('keuangan')->insert([
                'id_user' => session()->get('user_id'),
                'tipe' => 'pengeluaran',
                'kategori' => 'retur_penjualan',
                'tipe_ref' => 'retur_penjualan',
                'id_ref' => $retur_id,
                'jumlah' => $total_retur,
                'tanggal_transaksi' => $this->request->getPost('tanggal_retur'),
                'created_at' => $now
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return redirect()->to('/admin/retur')->with('success', 'Retur penjualan berhasil disimpan');

        } catch (\Exception $e) {
            $db->transRollback();
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

    // Batalkan retur (jika perlu)
    public function delete($id)
    {
        $retur = $this->returModel->find($id);
        if (!$retur) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $this->db->transStart();

        try {
            // Ambil detail retur
            $detail = $this->detailReturModel->getByRetur($id);

            foreach ($detail as $item) {
                // Kembalikan stok ke semula (kurangi stok yang sudah ditambahkan)
                $produk = $this->produkModel->find($item['id_produk']);
                $stok_sebelum = $produk['stok'];
                $stok_sesudah = $stok_sebelum - $item['jumlah'];

                $this->produkModel->update($item['id_produk'], ['stok' => $stok_sesudah]);

                // Log stok pembatalan retur
                $this->logStokModel->insert([
                    'id_produk' => $item['id_produk'],
                    'id_user' => session()->get('user_id'),
                    'tipe_ref' => 'retur_penjualan_batal',
                    'id_ref' => $id,
                    'jumlah_sebelum' => $stok_sebelum,
                    'jumlah_perubahan' => -$item['jumlah'],
                    'jumlah_sesudah' => $stok_sesudah,
                    'aktivitas' => 'Pembatalan retur penjualan',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Hapus keuangan
            $this->keuanganModel->where('tipe_ref', 'retur_penjualan')->where('id_ref', $id)->delete();

            // Hapus detail dan retur
            $this->detailReturModel->where('id_retur', $id)->delete();
            $this->returModel->delete($id);

            $this->db->transComplete();

            return redirect()->to('/admin/retur')->with('success', 'Retur berhasil dibatalkan');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }

    // Laporan Retur Penjualan
    public function laporan()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');
        $status = $this->request->getGet('status');

        $builder = $this->db->table('retur_penjualan')
            ->select('retur_penjualan.*, transaksi.no_invoice, users.username')
            ->join('transaksi', 'transaksi.id = retur_penjualan.id_transaksi')
            ->join('users', 'users.user_id = retur_penjualan.id_user', 'left')
            ->where('retur_penjualan.tanggal_retur >=', $start_date)
            ->where('retur_penjualan.tanggal_retur <=', $end_date);

        if ($status) {
            $builder->where('retur_penjualan.status', $status);
        }

        $retur = $builder->orderBy('retur_penjualan.tanggal_retur', 'DESC')->get()->getResultArray();

        // Statistik
        $totalRetur = count($retur);
        $totalNominalRetur = array_sum(array_column($retur, 'total_retur'));
        $rataRetur = $totalRetur > 0 ? $totalNominalRetur / $totalRetur : 0;

        // Top produk yang sering diretur
        $topProdukDiretur = $this->db->table('detail_retur_penjualan')
            ->select('detail_retur_penjualan.nama_produk, SUM(detail_retur_penjualan.jumlah) as total_jumlah, SUM(detail_retur_penjualan.subtotal) as total_nominal')
            ->groupBy('detail_retur_penjualan.nama_produk')
            ->orderBy('total_jumlah', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Retur per bulan
        $returPerBulan = $this->db->table('retur_penjualan')
            ->select('DATE_FORMAT(tanggal_retur, "%Y-%m") as bulan, COUNT(*) as jumlah, SUM(total_retur) as total')
            ->groupBy('DATE_FORMAT(tanggal_retur, "%Y-%m")')
            ->orderBy('bulan', 'DESC')
            ->get()
            ->getResultArray();

        // Alasan retur terbanyak
        $alasanTerbanyak = $this->db->table('retur_penjualan')
            ->select('alasan, COUNT(*) as jumlah, SUM(total_retur) as total')
            ->groupBy('alasan')
            ->orderBy('jumlah', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Laporan Retur Penjualan',
            'retur' => $retur,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $status,
            'total_retur' => $totalRetur,
            'total_nominal_retur' => $totalNominalRetur,
            'rata_retur' => $rataRetur,
            'top_produk_diretur' => $topProdukDiretur,
            'retur_per_bulan' => $returPerBulan,
            'alasan_terbanyak' => $alasanTerbanyak
        ];

        return view('admin/retur/laporan', $data);
    }

    // Export Excel Laporan Retur
    public function exportExcel()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');

        $retur = $this->db->table('retur_penjualan')
            ->select('retur_penjualan.*, transaksi.no_invoice, users.username')
            ->join('transaksi', 'transaksi.id = retur_penjualan.id_transaksi')
            ->join('users', 'users.user_id = retur_penjualan.id_user', 'left')
            ->where('retur_penjualan.tanggal_retur >=', $start_date)
            ->where('retur_penjualan.tanggal_retur <=', $end_date)
            ->orderBy('retur_penjualan.tanggal_retur', 'DESC')
            ->get()
            ->getResultArray();

        // Load library Excel (pastikan sudah install phpoffice/phpspreadsheet)
        // Untuk sementara, export ke CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_retur_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['No Retur', 'No Invoice', 'Tanggal Retur', 'Total Retur', 'Alasan', 'User']);

        foreach ($retur as $item) {
            fputcsv($output, [
                $item['no_retur'],
                $item['no_invoice'],
                date('d-m-Y', strtotime($item['tanggal_retur'])),
                number_format($item['total_retur'], 0, ',', '.'),
                $item['alasan'],
                $item['username'] ?? '-'
            ]);
        }

        fclose($output);
        exit();
    }
}