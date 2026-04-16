<?php
namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\LogStokModel;

class Stok extends BaseController
{
    protected $produkModel;
    protected $kategoriModel;
    protected $logStokModel;
    protected $db;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role != 'gudang' && $role != 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $this->db = \Config\Database::connect();
        $this->produkModel = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        $this->logStokModel = new LogStokModel();
    }

    // Halaman utama manajemen stok
    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $kategori_id = $this->request->getGet('kategori_id');
        $status_stok = $this->request->getGet('status_stok');

        $builder = $this->db->table('produk')
            ->select('produk.*, kategori.nama as nama_kategori')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left');

        // Filter pencarian
        if ($keyword) {
            $builder->groupStart()
                ->like('produk.nama_barang', $keyword)
                ->orLike('produk.sku', $keyword)
                ->groupEnd();
        }

        // Filter kategori
        if ($kategori_id) {
            $builder->where('produk.id_kategori', $kategori_id);
        }

        // Filter status stok
        if ($status_stok == 'menipis') {
            $builder->where('produk.stok <=', 'produk.min_stok', false);
        } elseif ($status_stok == 'habis') {
            $builder->where('produk.stok', 0);
        } elseif ($status_stok == 'aman') {
            $builder->where('produk.stok >', 'produk.min_stok', false);
        }

        $produk = $builder->orderBy('produk.id', 'DESC')->get()->getResultArray();

        // Hitung statistik - PERBAIKAN DI SINI
        $totalProduk = $this->produkModel->countAll();
        $stokMenipis = $this->produkModel->where('stok <=', 'min_stok', false)->countAllResults();
        $stokHabis = $this->produkModel->where('stok', 0)->countAllResults();

        // PERBAIKAN: Hitung total nilai stok dengan Query Builder
        $totalNilai = $this->db->table('produk')
            ->select('SUM(stok * harga_beli) as total')
            ->get()
            ->getRow();
        $totalNilaiStok = $totalNilai->total ?? 0;

        $data = [
            'title' => 'Manajemen Stok',
            'produk' => $produk,
            'kategori' => $this->kategoriModel->findAll(),
            'keyword' => $keyword,
            'kategori_id' => $kategori_id,
            'status_stok' => $status_stok,
            'total_produk' => $totalProduk,
            'stok_menipis' => $stokMenipis,
            'stok_habis' => $stokHabis,
            'total_nilai_stok' => $totalNilaiStok
        ];

        return view('gudang/stok/index', $data);
    }

    // Detail stok produk
    public function detail($id)
    {
        $produk = $this->db->table('produk')
            ->select('produk.*, kategori.nama as nama_kategori, supplier.nama as nama_supplier')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->join('supplier', 'supplier.id = produk.id_supplier', 'left')
            ->where('produk.id', $id)
            ->get()
            ->getRowArray();

        if (!$produk) {
            return redirect()->to('/gudang/stok')->with('error', 'Produk tidak ditemukan');
        }

        // Ambil histori stok
        $logStok = $this->db->table('log_stok')
            ->select('log_stok.*, users.username')
            ->join('users', 'users.user_id = log_stok.id_user', 'left')
            ->where('id_produk', $id)
            ->orderBy('created_at', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Detail Stok Produk',
            'produk' => $produk,
            'log_stok' => $logStok
        ];

        return view('gudang/stok/detail', $data);
    }

    // Form stok opname
    public function opname($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->to('/gudang/stok')->with('error', 'Produk tidak ditemukan');
        }

        $data = [
            'title' => 'Stok Opname',
            'produk' => $produk
        ];

        return view('gudang/stok/opname', $data);
    }

    // Proses update stok opname
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

        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->to('/gudang/stok')->with('error', 'Produk tidak ditemukan');
        }

        $stok_fisik = $this->request->getPost('stok_fisik');
        $stok_sebelum = $produk['stok'];
        $perubahan = $stok_fisik - $stok_sebelum;
        $keterangan = $this->request->getPost('keterangan');

        if ($perubahan == 0) {
            return redirect()->to('/gudang/stok/detail/' . $id)->with('info', 'Stok sudah sesuai, tidak ada perubahan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update stok produk
            $this->produkModel->update($id, ['stok' => $stok_fisik]);

            // Catat log stok
            $this->logStokModel->insert([
                'id_produk' => $id,
                'id_user' => session()->get('user_id'),
                'tipe_ref' => 'penyesuaian',
                'id_ref' => null,
                'jumlah_sebelum' => $stok_sebelum,
                'jumlah_perubahan' => $perubahan,
                'jumlah_sesudah' => $stok_fisik,
                'aktivitas' => 'Stok opname - ' . $keterangan,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            $message = $perubahan > 0
                ? "Stok bertambah " . abs($perubahan) . " unit"
                : "Stok berkurang " . abs($perubahan) . " unit";

            return redirect()->to('/gudang/stok/detail/' . $id)->with('success', $message);

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal update stok: ' . $e->getMessage());
        }
    }

    // Histori mutasi stok
    public function history()
    {
        $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');
        $produk_id = $this->request->getGet('produk_id');
        $tipe = $this->request->getGet('tipe');

        $builder = $this->db->table('log_stok')
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

        $log = $builder->orderBy('log_stok.created_at', 'DESC')->get()->getResultArray();

        // Statistik
        $totalMasuk = $this->db->table('log_stok')
            ->selectSum('jumlah_perubahan')
            ->where('tipe_ref', 'pembelian')
            ->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->get()
            ->getRowArray();

        $totalKeluar = $this->db->table('log_stok')
            ->selectSum('jumlah_perubahan')
            ->where('tipe_ref', 'penjualan')
            ->where('created_at >=', $start_date . ' 00:00:00')
            ->where('created_at <=', $end_date . ' 23:59:59')
            ->get()
            ->getRowArray();

        $data = [
            'title' => 'Histori Mutasi Stok',
            'log' => $log,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'produk_id' => $produk_id,
            'tipe' => $tipe,
            'produk_list' => $this->produkModel->findAll(),
            'total_masuk' => abs($totalMasuk['jumlah_perubahan'] ?? 0),
            'total_keluar' => abs($totalKeluar['jumlah_perubahan'] ?? 0)
        ];

        return view('gudang/stok/history', $data);
    }
}