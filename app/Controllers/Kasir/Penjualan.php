<?php
namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\ProdukModel;
use App\Models\LogStokModel;
use App\Models\KeuanganModel;

class Penjualan extends BaseController
{
    protected $transaksiModel;
    protected $detailTransaksiModel;
    protected $produkModel;
    protected $logStokModel;
    protected $keuanganModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
        $this->produkModel = new ProdukModel();
        $this->logStokModel = new LogStokModel();
        $this->keuanganModel = new KeuanganModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Penjualan / Kasir',
            'transaksi' => $this->transaksiModel->orderBy('id', 'DESC')->findAll()
        ];
        return view('admin/penjualan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Transaksi Penjualan',
            'no_invoice' => $this->generateNoInvoice(),
            'produk' => $this->produkModel->where('stok >', 0)->findAll()
        ];
        return view('admin/penjualan/create', $data);
    }

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

    public function store()
    {
        $rules = [
            'items' => 'required',
            'total_bayar' => 'required|numeric|greater_than[0]',
            'tipe_pembayaran' => 'required|in_list[tunai,transfer,debit]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = json_decode($this->request->getPost('items'), true);
        $total_bayar = $this->request->getPost('total_bayar');
        $total_belanja = $this->request->getPost('total_belanja');

        if ($total_bayar < $total_belanja) {
            return redirect()->back()->with('error', 'Pembayaran kurang dari total belanja');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Simpan transaksi
            $transaksiData = [
                'no_invoice' => $this->generateNoInvoice(),
                'id_user' => session()->get('user_id'),
                'total_bayar' => $total_bayar,
                'tipe_pembayaran' => $this->request->getPost('tipe_pembayaran'),
                'status' => 'selesai',
                'catatan' => $this->request->getPost('catatan')
            ];
            $this->transaksiModel->insert($transaksiData);
            $transaksi_id = $this->transaksiModel->getInsertID();

            // 2. Simpan detail & update stok
            foreach ($items as $item) {
                // Detail transaksi
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
                $stok_sebelum = $produk['stok'];
                $stok_sesudah = $stok_sebelum - $item['jumlah'];
                
                $this->produkModel->update($item['id_produk'], ['stok' => $stok_sesudah]);

                // Log stok
                $this->logStokModel->insert([
                    'id_produk' => $item['id_produk'],
                    'id_user' => session()->get('user_id'),
                    'tipe_ref' => 'penjualan',
                    'id_ref' => $transaksi_id,
                    'jumlah_sebelum' => $stok_sebelum,
                    'jumlah_perubahan' => -$item['jumlah'],
                    'jumlah_sesudah' => $stok_sesudah,
                    'aktivitas' => 'Penjualan ke customer'
                ]);
            }

            // 3. Catat keuangan (pemasukan)
            $this->keuanganModel->insert([
                'id_user' => session()->get('user_id'),
                'tipe' => 'pemasukan',
                'kategori' => 'penjualan',
                'tipe_ref' => 'penjualan',
                'id_ref' => $transaksi_id,
                'jumlah' => $total_belanja,
                'tanggal_transaksi' => date('Y-m-d')
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }

            return redirect()->to('/admin/penjualan/struk/' . $transaksi_id)->with('success', 'Transaksi berhasil!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function struk($id)
    {
        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi) {
            return redirect()->to('/admin/penjualan')->with('error', 'Transaksi tidak ditemukan');
        }
        
        $detail = $this->detailTransaksiModel->where('id_transaksi', $id)->findAll();

        $data = [
            'title' => 'Struk Pembayaran',
            'transaksi' => $transaksi,
            'detail' => $detail,
            'kembalian' => $transaksi['total_bayar'] - array_sum(array_column($detail, 'subtotal'))
        ];
        return view('admin/penjualan/struk', $data);
    }

    private function generateNoInvoice()
    {
        $last = $this->transaksiModel->orderBy('id', 'DESC')->first();
        $lastNumber = $last ? intval(substr($last['no_invoice'], -4)) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return 'INV-' . date('ymd') . '-' . $newNumber;
    }
}