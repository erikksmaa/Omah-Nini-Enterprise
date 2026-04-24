<?php
namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\PenjualanKasirModel;

class Penjualan extends BaseController
{
    protected $penjualanModel;
    protected $cancelTimeLimit = 60; // 60 menit

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role != 'kasir' && $role != 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $this->penjualanModel = new PenjualanKasirModel();
    }

    // Halaman utama kasir
    public function index()
    {
        $search = $this->request->getGet('search');
        $tipe_pembayaran = $this->request->getGet('tipe_pembayaran');
        $status = $this->request->getGet('status');
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 10;
        
        $builder = $this->penjualanModel->orderBy('id', 'DESC');
        
        if (!empty($search)) {
            $builder->like('no_invoice', $search);
        }
        
        if (!empty($tipe_pembayaran)) {
            $builder->where('tipe_pembayaran', $tipe_pembayaran);
        }
        
        if (!empty($status)) {
            $builder->where('status', $status);
        }
        
        if (!empty($start_date)) {
            $builder->where('DATE(created_at) >=', $start_date);
        }
        if (!empty($end_date)) {
            $builder->where('DATE(created_at) <=', $end_date);
        }
        
        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;
        $transaksi = $builder->limit($perPage, $offset)->findAll();
        
        // Hitung sisa waktu pembatalan untuk setiap transaksi
        foreach ($transaksi as &$item) {
            if ($item['status'] == 'selesai') {
                $createdAt = strtotime($item['created_at']);
                $now = time();
                $timeDiff = ($now - $createdAt) / 60;
                $remaining = max(0, $this->cancelTimeLimit - $timeDiff);
                $item['can_cancel'] = ($timeDiff <= $this->cancelTimeLimit);
                $item['remaining_minutes'] = round($remaining);
                $item['remaining_text'] = $this->formatRemainingTime($remaining);
            } else {
                $item['can_cancel'] = false;
                $item['remaining_minutes'] = 0;
                $item['remaining_text'] = '-';
            }
        }
        
        $pager = \Config\Services::pager();
        $pager->makeLinks($page, $perPage, $total, 'bootstrap_pagination');
        
        $data = [
            'title' => 'Kasir / Penjualan',
            'transaksi' => $transaksi,
            'pager' => $pager,
            'search' => $search,
            'tipe_pembayaran' => $tipe_pembayaran,
            'status' => $status,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total' => $total,
            'cancel_time_limit' => $this->cancelTimeLimit
        ];
        return view('kasir/penjualan/index', $data);
    }

    // Batalkan transaksi dengan batas waktu
    public function batal($id)
    {
        $isAdmin = (session()->get('role') == 'admin');
        $result = $this->penjualanModel->cancelPenjualanWithTimeLimit($id, session()->get('user_id'), $isAdmin);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['error']);
        }

        return redirect()->to('/kasir/penjualan')->with('success', $result['message']);
    }

    private function formatRemainingTime($minutes)
    {
        if ($minutes <= 0) return 'Kadaluarsa';
        
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'j ' . $mins . 'm';
        }
        return $mins . 'm';
    }

    // Halaman transaksi baru
    public function create()
    {
        $data = [
            'title' => 'Transaksi Penjualan',
            'no_invoice' => $this->penjualanModel->generateNoInvoice(),
            'produk' => $this->penjualanModel->getAvailableProducts()
        ];
        return view('kasir/penjualan/create', $data);
    }

    // Search produk (AJAX)
    public function searchProduk()
    {
        $keyword = $this->request->getGet('q');
        $produk = $this->penjualanModel->searchProducts($keyword);
        return $this->response->setJSON($produk);
    }

    // Proses simpan transaksi
    public function store()
    {
        $items = json_decode($this->request->getPost('items'), true);
        $total_belanja = $this->request->getPost('total_belanja');
        $bayar = $this->request->getPost('bayar');
        $tipe_pembayaran = $this->request->getPost('tipe_pembayaran');
        $catatan = $this->request->getPost('catatan');

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

        $result = $this->penjualanModel->savePenjualan(
            $items, $total_belanja, $bayar, $tipe_pembayaran, $catatan, session()->get('user_id')
        );

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $result['error']);
        }

        $total_belanja_detail = 0;
        foreach ($result['detail'] as $item) {
            $total_belanja_detail += $item['subtotal'];
        }

        session()->setFlashdata('show_struk', true);
        session()->setFlashdata('struk_data', [
            'transaksi' => $result['transaksi'],
            'detail' => $result['detail'],
            'total_belanja' => $total_belanja_detail,
            'kembalian' => $result['transaksi']['total_bayar'] - $total_belanja_detail,
            'kasir' => session()->get('username')
        ]);

        return redirect()->to('/kasir/penjualan')->with('success', 'Transaksi berhasil!');
    }

    // Halaman struk pembayaran
    public function struk($id)
    {
        $struk = $this->penjualanModel->getStrukDetail($id);
        if (!$struk) {
            return redirect()->to('/kasir/penjualan')->with('error', 'Transaksi tidak ditemukan');
        }

        $data = [
            'title' => 'Struk Pembayaran',
            'transaksi' => $struk['transaksi'],
            'detail' => $struk['detail'],
            'total_belanja' => $struk['total_belanja'],
            'kembalian' => $struk['kembalian'],
            'kasir' => session()->get('username')
        ];

        return view('kasir/penjualan/struk', $data);
    }

    // Get data struk untuk AJAX
    public function getStrukData($id)
    {
        $struk = $this->penjualanModel->getStrukData($id);
        
        if (!$struk) {
            return $this->response->setJSON(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $struk
        ]);
    }
}