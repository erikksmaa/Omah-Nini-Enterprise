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

   public function index()
{
    $keyword = $this->request->getGet('keyword');
    $kategori_id = $this->request->getGet('kategori_id');
    $status_stok = $this->request->getGet('status_stok');

    // Gunakan Model untuk pagination
    $builder = $this->produkModel
        ->select('produk.*, kategori.nama as nama_kategori')
        ->join('kategori', 'kategori.id = produk.id_kategori', 'left');

    if ($keyword) {
        $builder->groupStart()
            ->like('produk.nama_barang', $keyword)
            ->orLike('produk.sku', $keyword)
            ->groupEnd();
    }

    if ($kategori_id) {
        $builder->where('produk.id_kategori', $kategori_id);
    }

    if ($status_stok == 'menipis') {
        $builder->where('produk.stok <=', 'produk.min_stok', false);
    } elseif ($status_stok == 'habis') {
        $builder->where('produk.stok', 0);
    } elseif ($status_stok == 'aman') {
        $builder->where('produk.stok >', 'produk.min_stok', false);
    }

    // Paginate menggunakan Model
    $produk = $builder->orderBy('produk.id', 'DESC')->paginate(10);
    $pager = $this->produkModel->pager;

    // Statistik
    $totalProduk = $this->produkModel->countAll();
    $stokMenipis = $this->produkModel->where('stok <=', 'min_stok', false)->countAllResults();
    $stokHabis = $this->produkModel->where('stok', 0)->countAllResults();

    $totalNilai = $this->db->table('produk')
        ->select('SUM(stok * harga_beli) as total')
        ->get()
        ->getRow();
    $totalNilaiStok = $totalNilai->total ?? 0;

    $data = [
        'title' => 'Manajemen Stok',
        'produk' => $produk,
        'pager' => $pager,
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
            $this->produkModel->update($id, ['stok' => $stok_fisik]);

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

  public function history()
{
    $start_date = $this->request->getGet('start_date') ?? date('Y-m-01');
    $end_date = $this->request->getGet('end_date') ?? date('Y-m-d');
    $produk_id = $this->request->getGet('produk_id');
    $tipe = $this->request->getGet('tipe');
    
    // Pagination manual
    $page = $this->request->getGet('page') ?? 1;
    $perPage = 20;
    $offset = ($page - 1) * $perPage;
    
    // Hitung total data
    $builderCount = $this->db->table('log_stok')
        ->select('COUNT(*) as total')
        ->join('produk', 'produk.id = log_stok.id_produk')
        ->join('users', 'users.user_id = log_stok.id_user', 'left')
        ->where('log_stok.created_at >=', $start_date . ' 00:00:00')
        ->where('log_stok.created_at <=', $end_date . ' 23:59:59');
    
    if ($produk_id) {
        $builderCount->where('log_stok.id_produk', $produk_id);
    }
    
    if ($tipe) {
        $builderCount->where('log_stok.tipe_ref', $tipe);
    }
    
    $totalData = $builderCount->get()->getRow()->total ?? 0;
    $totalPages = ceil($totalData / $perPage);
    
    // Ambil data dengan limit
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
    
    $log = $builder->orderBy('log_stok.created_at', 'DESC')
        ->limit($perPage, $offset)
        ->get()
        ->getResultArray();
    
    // Buat pager manual
    $pager = \Config\Services::pager();
    $pager->makeLinks($page, $perPage, $totalData, 'bootstrap_pagination');
    
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
        'pager' => $pager,
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