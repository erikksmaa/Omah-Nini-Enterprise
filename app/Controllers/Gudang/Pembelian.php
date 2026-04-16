<?php
namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\PembelianModel;
use App\Models\DetailPembelianModel;
use App\Models\ProdukModel;
use App\Models\SupplierModel;
use App\Models\LogStokModel;
use App\Models\KeuanganModel;

class Pembelian extends BaseController
{
    protected $pembelianModel;
    protected $detailPembelianModel;
    protected $produkModel;
    protected $supplierModel;
    protected $logStokModel;
    protected $keuanganModel;

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
        $this->detailPembelianModel = new DetailPembelianModel();
        $this->produkModel = new ProdukModel();
        $this->supplierModel = new SupplierModel();
        $this->logStokModel = new LogStokModel();
        $this->keuanganModel = new KeuanganModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $pembelian = $db->table('pembelian')
            ->select('pembelian.*, supplier.nama as nama_supplier')  // ← alias 'nama_supplier'
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->orderBy('pembelian.id', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Data Pembelian Barang',
            'pembelian' => $pembelian
        ];

        return view('gudang/pembelian/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Form Pembelian Barang',
            'supplier' => $this->supplierModel->findAll(),
            'produk' => $this->produkModel->findAll(),
            'no_invoice' => $this->generateNoInvoice()
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

        $no_invoice = $this->generateNoInvoice();
        $now = date('Y-m-d H:i:s');

        // SIMPAN KE PEMBELIAN
        $pembelianData = [
            'no_invoice' => $no_invoice,
            'id_supplier' => $this->request->getPost('id_supplier'),
            'id_user' => session()->get('user_id'),
            'tanggal_pembelian' => $this->request->getPost('tanggal_pembelian'),
            'total_harga' => $total_harga,
            'catatan' => $this->request->getPost('catatan'),
            'created_at' => $now
        ];

        // Insert manual dengan Query Builder
        $db = \Config\Database::connect();

        try {
            // Insert pembelian
            $db->table('pembelian')->insert($pembelianData);
            $pembelian_id = $db->insertID();

            // Insert detail
            foreach ($items as $item) {
                $detailData = [
                    'id_pembelian' => $pembelian_id,
                    'id_produk' => $item['id_produk'],
                    'nama_produk' => $item['nama_produk'],
                    'jumlah' => $item['jumlah'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $item['subtotal']
                ];
                $db->table('detail_pembelian')->insert($detailData);

                // Update stok
                $produk = $db->table('produk')->where('id', $item['id_produk'])->get()->getRowArray();
                $stok_baru = $produk['stok'] + $item['jumlah'];
                $db->table('produk')->where('id', $item['id_produk'])->update([
                    'stok' => $stok_baru,
                    'harga_beli' => $item['harga_beli']
                ]);

                // Log stok
                $db->table('log_stok')->insert([
                    'id_produk' => $item['id_produk'],
                    'id_user' => session()->get('user_id'),
                    'tipe_ref' => 'pembelian',
                    'id_ref' => $pembelian_id,
                    'jumlah_sebelum' => $produk['stok'],
                    'jumlah_perubahan' => $item['jumlah'],
                    'jumlah_sesudah' => $stok_baru,
                    'aktivitas' => 'Pembelian barang',
                    'created_at' => $now
                ]);
            }

            // Insert keuangan
            $db->table('keuangan')->insert([
                'id_user' => session()->get('user_id'),
                'tipe' => 'pengeluaran',
                'kategori' => 'pembelian',
                'tipe_ref' => 'pembelian',
                'id_ref' => $pembelian_id,
                'jumlah' => $total_harga,
                'tanggal_transaksi' => $this->request->getPost('tanggal_pembelian'),
                'created_at' => $now
            ]);

            return redirect()->to('/gudang/pembelian')->with('success', 'Pembelian berhasil disimpan');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $db = \Config\Database::connect();

        $pembelian = $db->table('pembelian')
            ->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->where('pembelian.id', $id)
            ->get()
            ->getRowArray();

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

    private function generateNoInvoice()
    {
        $last = $this->pembelianModel->orderBy('id', 'DESC')->first();
        if ($last && isset($last['no_invoice'])) {
            $lastNumber = (int) substr($last['no_invoice'], -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        return 'PO-' . date('ymd') . '-' . $newNumber;
    }
}