<?php

namespace App\Controllers\Karyawan;

use App\Controllers\BaseController;
use App\Models\PembelianModel;
use App\Models\DetailPembelianModel;
use App\Models\SupplierModel;
use App\Models\ProdukModel;

class Pembelian extends BaseController
{
    public function index()
    {
        $pembelianModel = new PembelianModel();
        $data = [
            'title' => 'Riwayat Barang Masuk',
            'pembelian' => $pembelianModel->getAllWithSupplier(10),
            'pager' => $pembelianModel->pager,
        ];

        return view('karyawan/pembelian/index', $data);
    }

    public function create()
{
    $supplierModel = new SupplierModel();
    $produkModel = new ProdukModel();

    return view('karyawan/pembelian/create', [
        'title'         => 'Tambah Barang Masuk',
        'suppliers'     => $supplierModel->findAll(),
        'produk_list'   => $produkModel->getAllForDropdown(),
        'no_invoice'    => (new PembelianModel())->generateNoInvoice(),
    ]);
}

    public function store()
    {
        $pembelianModel = new PembelianModel();
        $produkModel = new ProdukModel();
        $userId = session()->get('user_id');

        // Validasi header
        $rules = [
            'no_invoice' => 'required|is_unique[pembelian.no_invoice]',
            'id_supplier' => 'required|is_not_unique[supplier.id]',
            'tanggal_pembelian' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = $this->request->getPost('items'); // array of ['id_produk', 'jumlah']
        // dd($items); // atau var_dump($items); exit;

        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Minimal satu item produk harus diisi.');
        }

        // Siapkan data items dengan nama_produk
        $itemsFormatted = [];
        foreach ($items as $i => $item) {

            $idProduk = $item['id_produk'] ?? null;
            $jumlah = $item['jumlah'] ?? 0;

            if (empty($idProduk) || $jumlah <= 0) {
                return redirect()->back()->withInput()->with('error', "Item ke-" . ($i + 1) . " tidak valid.");
            }

            // Ambil data produk
            $produk = $produkModel->getFullData($idProduk);
            if (!$produk) {
                return redirect()->back()->withInput()->with('error', "Produk dengan ID {$idProduk} tidak ditemukan.");
            }

            // Buat nama_produk dari SKU, motif, warna
            $namaProduk = $produk['sku'] . ' - ' . $produk['nama_motif'] . ' ' . $produk['nama_warna'];

            $itemsFormatted[] = [
                'id_produk'   => $idProduk,
                'nama_produk' => $namaProduk,
                'jumlah'      => $jumlah
            ];
        }

        $headerData = [
            'no_invoice' => $this->request->getPost('no_invoice'),
            'id_supplier' => $this->request->getPost('id_supplier'),
            'id_user' => $userId,
            'tanggal_pembelian' => $this->request->getPost('tanggal_pembelian'),
            'catatan' => $this->request->getPost('catatan'),
        ];

        $result = $pembelianModel->savePembelian($headerData, $itemsFormatted, $userId);

        if ($result['success']) {
            return redirect()->to('/karyawan/pembelian/detail/' . $result['id'])
                ->with('success', 'Barang masuk berhasil dicatat.');
        } else {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }
    }

    public function detail($id)
    {
        $pembelianModel = new PembelianModel();
        $detailPembelianModel = new DetailPembelianModel();

        $header = $pembelianModel->getDetail($id);
        if (!$header) {
            return redirect()->to('/karyawan/pembelian')->with('error', 'Data pembelian tidak ditemukan.');
        }

        $items = $detailPembelianModel->getWithProductInfo($id);

        $data = [
            'title' => 'Detail Barang Masuk #' . $header['no_invoice'],
            'header' => $header,
            'items' => $items,
        ];

        return view('karyawan/pembelian/detail', $data);
    }
}
