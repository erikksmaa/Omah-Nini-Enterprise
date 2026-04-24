<?php
namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\LogStokModel;

class Stok extends BaseController
{
    protected $stokModel;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role != 'gudang' && $role != 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        // Gunakan LogStokModel yang sudah digabung
        $this->stokModel = new LogStokModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $kategori_id = $this->request->getGet('kategori_id');
        $status_stok = $this->request->getGet('status_stok');

        $produk = $this->stokModel->getStokList($keyword, $kategori_id, $status_stok, 10);
        $pager = $this->stokModel->getPager();
        $statistik = $this->stokModel->getStokStatistics();

        $data = [
            'title' => 'Manajemen Stok',
            'produk' => $produk,
            'pager' => $pager,
            'kategori' => $this->stokModel->getKategoriList(),
            'keyword' => $keyword,
            'kategori_id' => $kategori_id,
            'status_stok' => $status_stok,
            'total_produk' => $statistik['total_produk'],
            'stok_menipis' => $statistik['stok_menipis'],
            'stok_habis' => $statistik['stok_habis'],
            'total_nilai_stok' => $statistik['total_nilai_stok']
        ];

        return view('gudang/stok/index', $data);
    }

    public function detail($id)
    {
        $detail = $this->stokModel->getProductDetail($id);
        
        if (!$detail) {
            return redirect()->to('/gudang/stok')->with('error', 'Produk tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Stok Produk',
            'produk' => $detail['produk'],
            'log_stok' => $detail['log_stok']
        ];

        return view('gudang/stok/detail', $data);
    }

    public function opname($id)
    {
        $produk = $this->stokModel->getProductForOpname($id);
        if (!$produk) {
            return redirect()->to('/gudang/stok')->with('error', 'Produk tidak ditemukan');
        }

        $data = [
            'title' => 'Stok Opname',
            'produk' => $produk
        ];

        return view('gudang/stok/opname', $data);
    }

    public function updateOpname($id)
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'stok_fisik' => 'required|numeric|greater_than_equal_to[0]',
            'keterangan' => 'permit_empty|max_length[255]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $stok_fisik = $this->request->getPost('stok_fisik');
        $keterangan = $this->request->getPost('keterangan');

        $result = $this->stokModel->updateOpname($id, $stok_fisik, $keterangan, session()->get('user_id'));

        if (!$result['success']) {
            return redirect()->back()->with('error', 'Gagal update stok: ' . $result['error']);
        }

        if ($result['perubahan'] == 0) {
            return redirect()->to('/gudang/stok/detail/' . $id)->with('info', $result['message']);
        }

        return redirect()->to('/gudang/stok/detail/' . $id)->with('success', $result['message']);
    }

    public function history()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');
        $produk_id = $this->request->getGet('produk_id');
        $tipe = $this->request->getGet('tipe');
        $page = $this->request->getGet('page') ?? 1;
        
        $history = $this->stokModel->getStockHistory($start_date, $end_date, $produk_id, $tipe, $page, 20);
        
        // Buat pager
        $pager = \Config\Services::pager();
        $pager->makeLinks($history['current_page'], $history['per_page'], $history['total_data'], 'bootstrap_pagination');

        $data = [
            'title' => 'Histori Mutasi Stok',
            'log' => $history['log'],
            'pager' => $pager,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'produk_id' => $produk_id,
            'tipe' => $tipe,
            'produk_list' => $this->stokModel->getProductList(),
            'total_masuk' => $history['total_masuk'],
            'total_keluar' => $history['total_keluar']
        ];

        return view('gudang/stok/history', $data);
    }
}