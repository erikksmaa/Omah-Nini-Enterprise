<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\SupplierModel;
use App\Models\MotifModel;
use App\Models\WarnaModel;
use App\Models\DetailPembelianModel;
use App\Models\DetailTransaksiModel;

class Produk extends BaseController
{
    protected $produkModel;
    protected $supplierModel;
    protected $motifModel;
    protected $warnaModel;

    public function __construct()
    {
        helper("image");
        $this->produkModel   = new ProdukModel();
        $this->supplierModel = new SupplierModel();
        $this->motifModel    = new MotifModel();
        $this->warnaModel    = new WarnaModel();
    }

    // =========================================================
    //  INDEX — Tabel flat + paginasi + filter server-side
    // =========================================================
    public function index()
    {
        $supplierId = $this->request->getGet('supplier');
        $keyword    = $this->request->getGet('keyword');

        // ── Query utama (paginasi) ──────────────────────────
        $builder = $this->produkModel
            ->select('produk.*, supplier.nama as nama_supplier, supplier.id as supplier_id,
                      motif.nama_motif, motif.id as motif_id,
                      warna.nama_warna, warna.id as warna_id')
            ->join('supplier', 'supplier.id = produk.id_supplier')
            ->join('motif',    'motif.id    = produk.id_motif')
            ->join('warna',    'warna.id    = produk.id_warna');

        if (!empty($supplierId)) {
            $builder->where('produk.id_supplier', $supplierId);
        }

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('produk.sku',         $keyword)
                ->orLike('motif.nama_motif', $keyword)
                ->orLike('warna.nama_warna', $keyword)
                ->orLike('supplier.nama',    $keyword)
            ->groupEnd();
        }

        $produk = $builder
            ->orderBy('supplier.nama',    'ASC')
            ->orderBy('motif.nama_motif', 'ASC')
            ->orderBy('warna.nama_warna', 'ASC')
            ->paginate(15);

        // ── Summary global (tidak terpengaruh filter) ───────
        $allProducts = $this->produkModel
            ->select('produk.id, produk.stok, produk.id_supplier, produk.id_motif')
            ->findAll();

        $data = [
            'title'            => 'Master Produk',
            'produk'           => $produk,
            'pager'            => $this->produkModel->pager,
            'suppliers'        => $this->supplierModel->findAll(),
            'selectedSupplier' => $supplierId,
            'keyword'          => $keyword,
            'total_merek'      => count(array_unique(array_column($allProducts, 'id_supplier'))),
            'total_motif'      => count(array_unique(array_column($allProducts, 'id_motif'))),
            'total_sku'        => count($allProducts),
            'total_stok'       => (int) array_sum(array_column($allProducts, 'stok')),
        ];

        return view('admin/produk/index', $data);
    }

    // =========================================================
    //  Selebihnya tidak berubah
    // =========================================================

    private function getMotifBySupplierForFilter($id_supplier)
    {
        if (empty($id_supplier)) {
            return [];
        }
        return $this->motifModel->where('id_supplier', $id_supplier)->findAll();
    }

    public function create()
    {
        $data = [
            'title'     => 'Tambah Produk Baru',
            'suppliers' => $this->supplierModel->getOptions(),
            'motifs'    => $this->motifModel->getOptions(),
            'warnas'    => $this->warnaModel->getOptions(),
        ];

        return view('admin/produk/create', $data);
    }

    public function store()
    {
        $sku = $this->request->getPost('sku');

        if (empty($sku)) {
            $id_supplier = $this->request->getPost('id_supplier');
            $id_motif    = $this->request->getPost('id_motif');
            $id_warna    = $this->request->getPost('id_warna');

            if (!empty($id_supplier) && !empty($id_motif) && !empty($id_warna)) {
                $sku = $this->produkModel->generateSku($id_supplier, $id_motif, $id_warna);
            }
        }

        $_POST['sku'] = $sku;

        if (!$this->validate($this->produkModel->validationRules, $this->produkModel->validationMessages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $fotoName = null;
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $fotoName = uploadAndResizeImage($fotoFile);
        }

        try {
            $this->produkModel->save([
                'sku'         => $sku,
                'id_supplier' => $this->request->getPost('id_supplier'),
                'id_motif'    => $this->request->getPost('id_motif'),
                'id_warna'    => $this->request->getPost('id_warna'),
                'stok'        => $this->request->getPost('stok') ?? 0,
                'min_stok'    => $this->request->getPost('min_stok') ?? 0,
                'keterangan'  => $this->request->getPost('keterangan'),
                'foto'        => $fotoName,
            ]);

            return redirect()->to('/admin/produk')
                ->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $produk = $this->produkModel->getByIdWithRelations($id);
        if (!$produk) {
            return redirect()->to('/admin/produk')
                ->with('error', 'Produk tidak ditemukan.');
        }

        $data = [
            'title'     => 'Edit Produk',
            'produk'    => $produk,
            'suppliers' => $this->supplierModel->getOptions(),
            'motifs'    => $this->motifModel->getOptions(),
            'warnas'    => $this->warnaModel->getOptions(),
        ];

        return view('admin/produk/edit', $data);
    }

    public function update($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->back()
                ->with('error', 'Data produk tidak ditemukan.');
        }

        $newSku = trim($this->request->getPost('sku'));
        $oldSku = trim($produk['sku']);

        $error = null;
        if ($newSku !== $oldSku) {
            $existing = $this->produkModel->where('sku', $newSku)->where('id !=', $id)->first();
            if ($existing) {
                $error = 'SKU sudah terdaftar. Gunakan SKU yang berbeda.';
            }
        }

        $rules = [
            'id_supplier' => 'required|numeric',
            'id_motif'    => 'required|numeric',
            'id_warna'    => 'required|numeric',
            'stok'        => 'required|numeric|greater_than_equal_to[0]',
            'min_stok'    => 'permit_empty|numeric|greater_than_equal_to[0]',
            'foto'        => 'permit_empty|is_image[foto]|max_size[foto,5120]',
        ];

        $messages = [
            'id_supplier' => ['required' => 'Supplier wajib dipilih.'],
            'id_motif'    => ['required' => 'Motif wajib dipilih.'],
            'id_warna'    => ['required' => 'Warna wajib dipilih.'],
            'stok'        => [
                'required'              => 'Stok wajib diisi.',
                'greater_than_equal_to' => 'Stok tidak boleh negatif.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        if ($error) {
            return redirect()->back()
                ->withInput()
                ->with('error', $error);
        }

        $updateData = [
            'sku'         => $this->request->getPost('sku'),
            'id_supplier' => $this->request->getPost('id_supplier'),
            'id_motif'    => $this->request->getPost('id_motif'),
            'id_warna'    => $this->request->getPost('id_warna'),
            'stok'        => $this->request->getPost('stok') ?? 0,
            'min_stok'    => $this->request->getPost('min_stok') ?? 0,
            'keterangan'  => $this->request->getPost('keterangan'),
        ];

        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $fotoName = uploadAndResizeImage($fotoFile, $produk['foto']);
            if ($fotoName) {
                $updateData['foto'] = $fotoName;
            }
        }

        try {
            $this->produkModel->update($id, $updateData);

            return redirect()->to('/admin/produk')
                ->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->back()
                ->with('error', 'Data produk tidak ditemukan.');
        }

        $detailPembelianModel = new DetailPembelianModel();
        $detailTransaksiModel = new DetailTransaksiModel();

        $pembelianCount = $detailPembelianModel->where('id_produk', $id)->countAllResults();
        $transaksiCount = $detailTransaksiModel->where('id_produk', $id)->countAllResults();

        if ($pembelianCount > 0 || $transaksiCount > 0) {
            return redirect()->back()
                ->with('error', 'Produk tidak bisa dihapus karena sudah memiliki riwayat transaksi.');
        }

        if (!empty($produk['foto'])) {
            deleteImage($produk['foto']);
        }

        try {
            $this->produkModel->delete($id);
            return redirect()->to('/admin/produk')
                ->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    public function getMotifBySupplier()
    {
        $id_supplier = $this->request->getPost('id_supplier');
        $motifs      = $this->motifModel->where('id_supplier', $id_supplier)->findAll();

        return $this->response->setJSON(['status' => 'success', 'data' => $motifs]);
    }

    public function getMotifBySupplierAjax()
    {
        $id_supplier = $this->request->getGet('id_supplier');
        $motifs      = $this->motifModel->where('id_supplier', $id_supplier)->findAll();

        return $this->response->setJSON(['status' => 'success', 'data' => $motifs]);
    }

    public function generateSku()
    {
        $id_supplier = $this->request->getPost('id_supplier');
        $id_motif    = $this->request->getPost('id_motif');
        $id_warna    = $this->request->getPost('id_warna');

        if (empty($id_supplier)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Supplier harus dipilih terlebih dahulu.']);
        }
        if (empty($id_motif)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Motif harus dipilih terlebih dahulu.']);
        }
        if (empty($id_warna)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Warna harus dipilih terlebih dahulu.']);
        }

        $sku = $this->produkModel->generateSku($id_supplier, $id_motif, $id_warna);

        return $this->response->setJSON(['status' => 'success', 'sku' => $sku]);
    }
}