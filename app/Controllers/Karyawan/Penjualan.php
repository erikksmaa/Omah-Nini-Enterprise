<?php
namespace App\Controllers\Karyawan;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\ProdukModel;
use App\Models\PelangganModel;

class Penjualan extends BaseController
{
    public function index()
    {
        $transaksiModel = new TransaksiModel();
        $data = [
            'title'       => 'Riwayat Penjualan',
            'transaksi'   => $transaksiModel->getAllWithPelanggan(10),
            'pager'       => $transaksiModel->pager,
        ];

        return view('karyawan/penjualan/index', $data);
    }

    public function create()
    {
        $produkModel = new ProdukModel();
        $pelangganModel = new PelangganModel();

        $data = [
            'title'         => 'Barang Keluar (POS)',
            'produk_list'   => $produkModel->getAvailableProducts(), // hanya produk dengan stok > 0
            'pelanggan_list'=> $pelangganModel->findAll(),
            'no_invoice'    => (new TransaksiModel())->generateNoInvoice(),
        ];

        return view('karyawan/penjualan/create', $data);
    }

    public function store()
    {
        $transaksiModel = new TransaksiModel();
        $produkModel = new ProdukModel();
        $userId = session()->get('user_id');

        // Validasi header
        $rules = [
            'no_invoice'        => 'required|is_unique[transaksi.no_invoice]',
            'nama_pembeli'      => 'required|min_length[2]',
            'tanggal_transaksi' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = $this->request->getPost('items'); // array of ['id_produk', 'jumlah']

        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Minimal satu item produk harus diisi.');
        }

        // Format items & validasi
        $itemsFormatted = [];
        foreach ($items as $i => $item) {
            $idProduk = $item['id_produk'] ?? null;
            $jumlah   = $item['jumlah'] ?? 0;

            if (empty($idProduk) || $jumlah <= 0) {
                return redirect()->back()->withInput()->with('error', "Item ke-".($i+1)." tidak valid.");
            }

            $produk = $produkModel->getFullData($idProduk);
            if (!$produk) {
                return redirect()->back()->withInput()->with('error', "Produk dengan ID {$idProduk} tidak ditemukan.");
            }

            // Validasi stok mencukupi (cek stok sekarang)
            if ($produk['stok'] < $jumlah) {
                return redirect()->back()->withInput()->with('error', "Stok tidak mencukupi untuk produk {$produk['sku']} - {$produk['nama_motif']} {$produk['nama_warna']}. Stok tersedia: {$produk['stok']}");
            }

            $namaProduk = $produk['sku'] . ' - ' . $produk['nama_motif'] . ' ' . $produk['nama_warna'];

            $itemsFormatted[] = [
                'id_produk'   => $idProduk,
                'nama_produk' => $namaProduk,
                'jumlah'      => $jumlah
            ];
        }

        // Data header
        $headerData = [
            'no_invoice'        => $this->request->getPost('no_invoice'),
            'id_user'           => $userId,
            'id_pelanggan'      => $this->request->getPost('id_pelanggan') ?: null,
            'nama_pembeli'      => $this->request->getPost('nama_pembeli'),
            'tanggal_transaksi' => $this->request->getPost('tanggal_transaksi'),
            'catatan'           => $this->request->getPost('catatan'),
        ];

        $result = $transaksiModel->saveTransaksi($headerData, $itemsFormatted, $userId);

        if ($result['success']) {
            return redirect()->to('/karyawan/penjualan/struk/' . $result['id'])
                ->with('success', 'Transaksi penjualan berhasil.');
        } else {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }
    }

    public function struk($id)
    {
        $transaksiModel = new TransaksiModel();
        $detailModel = new DetailTransaksiModel();

        $header = $transaksiModel->getDetail($id);
        if (!$header) {
            return redirect()->to('/karyawan/penjualan')->with('error', 'Transaksi tidak ditemukan.');
        }

        $items = $detailModel->getWithProductInfo($id);

        $data = [
            'title'  => 'Struk Penjualan #' . $header['no_invoice'],
            'header' => $header,
            'items'  => $items,
        ];

        return view('karyawan/penjualan/struk', $data);
    }
}