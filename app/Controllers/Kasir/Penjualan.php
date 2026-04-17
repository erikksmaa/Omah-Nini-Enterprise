<?php
namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\LogStokModel;
use App\Models\KeuanganModel;

class Penjualan extends BaseController
{
    protected $produkModel;
    protected $transaksiModel;
    protected $detailTransaksiModel;
    protected $logStokModel;
    protected $keuanganModel;
    protected $db;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role != 'kasir' && $role != 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $this->db = \Config\Database::connect();
        $this->produkModel = new ProdukModel();
        $this->transaksiModel = new TransaksiModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
        $this->logStokModel = new LogStokModel();
        $this->keuanganModel = new KeuanganModel();
    }

    // Halaman utama kasir
    public function index()
    {
        $data = [
            'title' => 'Kasir / Penjualan',
            'transaksi' => $this->transaksiModel->orderBy('id', 'DESC')->limit(50)->findAll()
        ];
        return view('kasir/penjualan/index', $data);
    }

    // Halaman transaksi baru
    public function create()
    {
        $data = [
            'title' => 'Transaksi Penjualan',
            'no_invoice' => $this->generateNoInvoice(),
            'produk' => $this->produkModel->where('stok >', 0)->findAll()
        ];
        return view('kasir/penjualan/create', $data);
    }

    // Search produk (AJAX)
    public function searchProduk()
    {
        $keyword = $this->request->getGet('q');
        $produk = $this->produkModel->groupStart()
            ->like('nama_barang', $keyword)
            ->orLike('sku', $keyword)
            ->groupEnd()
            ->where('stok >', 0)
            ->findAll();

        return $this->response->setJSON($produk);
    }

  // Proses simpan transaksi
public function store()
{
    // Ambil data
    $items = json_decode($this->request->getPost('items'), true);
    $total_belanja = $this->request->getPost('total_belanja');
    $bayar = $this->request->getPost('bayar');
    $tipe_pembayaran = $this->request->getPost('tipe_pembayaran');

    // Validasi
    $errors = [];

    if (empty($items)) {
        $errors[] = 'Keranjang belanja kosong';
    }

    if (empty($total_belanja) || $total_belanja <= 0) {
        $errors[] = 'Total belanja tidak valid';
    }

    if (empty($bayar) || $bayar <= 0) {
        $errors[] = 'Jumlah pembayaran harus diisi';
    }

    if ($bayar < $total_belanja) {
        $errors[] = 'Pembayaran kurang dari total belanja (Kurang Rp ' . number_format($total_belanja - $bayar, 0, ',', '.') . ')';
    }

    if (!in_array($tipe_pembayaran, ['tunai', 'transfer', 'qris'])) {
        $errors[] = 'Tipe pembayaran tidak valid';
    }

    if (!empty($errors)) {
        return redirect()->back()->withInput()->with('errors', $errors);
    }

    $kembalian = $bayar - $total_belanja;
    $now = date('Y-m-d H:i:s');  // Format: 2026-04-17 14:30:00
    $no_invoice = $this->generateNoInvoice();

    // Mulai transaksi database
    $this->db->transStart();

    try {
        // PERBAIKAN: 1. Insert ke tabel transaksi
        $transaksiData = [
            'no_invoice' => $no_invoice,
            'id_user' => session()->get('user_id'),
            'tanggal_transaksi' => $now,  // ← PASTIKAN INI TERISI
            'total_bayar' => $bayar,
            'tipe_pembayaran' => $tipe_pembayaran,
            'status' => 'selesai',
            'catatan' => $this->request->getPost('catatan'),
            'created_at' => $now  // ← PASTIKAN INI TERISI
        ];
        
        $this->transaksiModel->insert($transaksiData);
        $transaksi_id = $this->transaksiModel->getInsertID();

        // Debug: Cek apakah insert berhasil
        log_message('debug', 'Transaksi ID: ' . $transaksi_id);
        log_message('debug', 'Tanggal transaksi: ' . $now);

        // 2. Insert detail & update stok
        foreach ($items as $item) {
            $this->detailTransaksiModel->insert([
                'id_transaksi' => $transaksi_id,
                'id_produk' => $item['id_produk'],
                'nama_produk' => $item['nama_produk'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga_jual'],
                'subtotal' => $item['subtotal']
            ]);

            // Update stok
            $produk = $this->produkModel->find($item['id_produk']);
            $stok_baru = $produk['stok'] - $item['jumlah'];
            $this->produkModel->update($item['id_produk'], ['stok' => $stok_baru]);

            // Log stok
            $this->logStokModel->insert([
                'id_produk' => $item['id_produk'],
                'id_user' => session()->get('user_id'),
                'tipe_ref' => 'penjualan',
                'id_ref' => $transaksi_id,
                'jumlah_sebelum' => $produk['stok'],
                'jumlah_perubahan' => -$item['jumlah'],
                'jumlah_sesudah' => $stok_baru,
                'aktivitas' => 'Penjualan ke customer',
                'created_at' => $now
            ]);
        }

        // 3. Insert keuangan
        $this->keuanganModel->insert([
            'id_user' => session()->get('user_id'),
            'tipe' => 'pemasukan',
            'kategori' => 'penjualan',
            'tipe_ref' => 'penjualan',
            'id_ref' => $transaksi_id,
            'jumlah' => $total_belanja,
            'tanggal_transaksi' => date('Y-m-d'),
            'created_at' => $now
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \Exception('Transaksi gagal');
        }

        // Ambil data transaksi untuk struk
        $transaksi = $this->transaksiModel->find($transaksi_id);
        $detail = $this->detailTransaksiModel->where('id_transaksi', $transaksi_id)->findAll();

        $total_belanja_detail = 0;
        foreach ($detail as $item) {
            $total_belanja_detail += $item['subtotal'];
        }

        // Simpan data struk ke flashdata
        session()->setFlashdata('show_struk', true);
        session()->setFlashdata('struk_data', [
            'transaksi' => $transaksi,
            'detail' => $detail,
            'total_belanja' => $total_belanja_detail,
            'kembalian' => $transaksi['total_bayar'] - $total_belanja_detail,
            'kasir' => session()->get('username')
        ]);

        return redirect()->to('/kasir/penjualan')->with('success', 'Transaksi berhasil!');

    } catch (\Exception $e) {
        $this->db->transRollback();
        log_message('error', 'Penjualan error: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}

    // Halaman struk pembayaran
    public function struk($id)
    {
        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi) {
            return redirect()->to('/kasir/penjualan')->with('error', 'Transaksi tidak ditemukan');
        }

        $detail = $this->detailTransaksiModel->where('id_transaksi', $id)->findAll();

        // Hitung total belanja dari detail
        $total_belanja = 0;
        foreach ($detail as $item) {
            $total_belanja += $item['subtotal'];
        }

        $kembalian = $transaksi['total_bayar'] - $total_belanja;

        $data = [
            'title' => 'Struk Pembayaran',
            'transaksi' => $transaksi,
            'detail' => $detail,
            'total_belanja' => $total_belanja,
            'kembalian' => $kembalian,
            'kasir' => session()->get('username')
        ];

        return view('kasir/penjualan/struk', $data);
    }

    // Generate nomor invoice
    private function generateNoInvoice()
    {
        $last = $this->transaksiModel->orderBy('id', 'DESC')->first();
        if ($last && isset($last['no_invoice'])) {
            $lastNumber = (int) substr($last['no_invoice'], -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        return 'INV-' . date('ymd') . '-' . $newNumber;
    }
    // Batalkan transaksi
    public function batal($id)
    {
        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan');
        }

        if ($transaksi['status'] == 'batal') {
            return redirect()->back()->with('error', 'Transaksi sudah dibatalkan');
        }

        $this->db->transStart();

        try {
            // Ambil detail transaksi
            $detail = $this->detailTransaksiModel->where('id_transaksi', $id)->findAll();

            foreach ($detail as $item) {
                // Kembalikan stok
                $produk = $this->produkModel->find($item['id_produk']);
                $stok_sebelum = $produk['stok'];
                $stok_sesudah = $stok_sebelum + $item['jumlah'];

                $this->produkModel->update($item['id_produk'], ['stok' => $stok_sesudah]);

                // Log stok pembatalan
                $this->logStokModel->insert([
                    'id_produk' => $item['id_produk'],
                    'id_user' => session()->get('user_id'),
                    'tipe_ref' => 'penjualan_batal',
                    'id_ref' => $id,
                    'jumlah_sebelum' => $stok_sebelum,
                    'jumlah_perubahan' => $item['jumlah'],
                    'jumlah_sesudah' => $stok_sesudah,
                    'aktivitas' => 'Pembatalan transaksi penjualan',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Update status transaksi
            $this->transaksiModel->update($id, ['status' => 'batal']);

            // Hapus catatan keuangan
            $this->keuanganModel->where('tipe_ref', 'penjualan')->where('id_ref', $id)->delete();

            $this->db->transComplete();

            return redirect()->to('/kasir/penjualan')->with('success', 'Transaksi berhasil dibatalkan');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }

    // Get data struk untuk AJAX
    public function getStrukData($id)
    {
        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi) {
            return $this->response->setJSON(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
        }

        $detail = $this->detailTransaksiModel->where('id_transaksi', $id)->findAll();

        $total_belanja = 0;
        foreach ($detail as $item) {
            $total_belanja += $item['subtotal'];
        }

        $data = [
            'success' => true,
            'data' => [
                'transaksi' => $transaksi,
                'detail' => $detail,
                'total_belanja' => $total_belanja,
                'kembalian' => $transaksi['total_bayar'] - $total_belanja,
                'kasir' => session()->get('username')
            ]
        ];

        return $this->response->setJSON($data);
    }
}