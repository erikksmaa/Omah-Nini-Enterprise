<?php
namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\PembelianModel;
use App\Models\SupplierModel;
use App\Models\ProdukModel;
use App\Models\DetailPembelianModel;

class Pembelian extends BaseController
{
    protected $pembelianModel;
    protected $supplierModel;
    protected $produkModel;
    protected $detailPembelianModel;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }

        $role = session()->get('role');
        if ($role != 'gudang' && $role != 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $this->pembelianModel = new PembelianModel();
        $this->supplierModel = new SupplierModel();
        $this->produkModel = new ProdukModel();
        $this->detailPembelianModel = new DetailPembelianModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $supplier_id = $this->request->getGet('supplier_id');
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $perPage = 10;

        $builder = $this->pembelianModel
            ->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->orderBy('pembelian.id', 'DESC');

        // Filter pencarian berdasarkan no_invoice
        if (!empty($keyword)) {
            $builder->like('pembelian.no_invoice', $keyword);
        }

        // Filter supplier
        if (!empty($supplier_id)) {
            $builder->where('pembelian.id_supplier', $supplier_id);
        }

        // Filter tanggal
        if (!empty($start_date)) {
            $builder->where('pembelian.tanggal_pembelian >=', $start_date);
        }
        if (!empty($end_date)) {
            $builder->where('pembelian.tanggal_pembelian <=', $end_date);
        }

        $pembelian = $builder->paginate($perPage);
        $pager = $this->pembelianModel->pager;

        $data = [
            'title' => 'Data Pembelian Barang',
            'pembelian' => $pembelian,
            'pager' => $pager,
            'supplier_list' => $this->supplierModel->findAll(),
            'keyword' => $keyword,
            'supplier_id' => $supplier_id,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        return view('gudang/pembelian/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Form Pembelian Barang',
            'supplier' => $this->supplierModel->findAll(),
            'produk' => $this->produkModel->findAll(),
            'no_invoice' => $this->pembelianModel->generateNoInvoice()
        ];

        return view('gudang/pembelian/create', $data);
    }

    public function store()
    {
        // Validasi
        $validation = \Config\Services::validation();

        $validation->setRules([
            'id_supplier' => 'required',
            'tanggal_pembelian' => 'required|valid_date',
            'items' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $items = json_decode($this->request->getPost('items'), true);

        if (empty($items)) {
            return redirect()->back()->with('error', 'Tidak ada produk yang dipilih');
        }

        $total_harga = 0;
        foreach ($items as $item) {
            $total_harga += $item['subtotal'];
        }

        $pembelianData = [
            'no_invoice' => $this->pembelianModel->generateNoInvoice(),
            'id_supplier' => $this->request->getPost('id_supplier'),
            'id_user' => session()->get('user_id'),
            'tanggal_pembelian' => $this->request->getPost('tanggal_pembelian'),
            'total_harga' => $total_harga,
            'catatan' => $this->request->getPost('catatan'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->pembelianModel->savePembelian($pembelianData, $items, session()->get('user_id'));

        if ($result['success']) {
            return redirect()->to('/gudang/pembelian')->with('success', 'Pembelian berhasil disimpan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $result['error']);
        }
    }

    public function detail($id)
    {
        $pembelian = $this->pembelianModel->getDetail($id);

        if (!$pembelian) {
            return redirect()->to('/gudang/pembelian')->with('error', 'Data tidak ditemukan');
        }

        $detail = $this->detailPembelianModel->where('id_pembelian', $id)->findAll();

        $data = [
            'title' => 'Detail Pembelian',
            'pembelian' => $pembelian,
            'detail' => $detail
        ];
        return view('gudang/pembelian/detail', $data);
    }
}