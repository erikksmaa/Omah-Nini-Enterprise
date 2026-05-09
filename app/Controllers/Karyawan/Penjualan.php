<?php

namespace App\Controllers\Karyawan;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\ProdukModel;
use App\Models\PelangganModel;
use App\Models\UserModel;
use App\Models\SupplierModel;

class Penjualan extends BaseController
{
    public function index()
    {
        $transaksiModel = new TransaksiModel();
        $userModel      = new UserModel();
        $supplierModel  = new SupplierModel();

        $tanggalMulai   = $this->request->getGet('tanggal_mulai');
        $tanggalAkhir   = $this->request->getGet('tanggal_akhir');
        $userIdFilter   = $this->request->getGet('user');
        $supplierFilter = $this->request->getGet('supplier');
        $namaPembeli    = $this->request->getGet('nama_pembeli');

        $builder = $transaksiModel
            ->select('transaksi.*, pelanggan.nama as nama_pelanggan, users.username as user_username')
            ->join('pelanggan', 'pelanggan.id = transaksi.id_pelanggan', 'left')
            ->join('users', 'users.user_id = transaksi.id_user')
            ->orderBy('transaksi.id', 'DESC');

        // Filter merek melalui join ke detail_transaksi & produk
        if (!empty($supplierFilter)) {
            $builder->join('detail_transaksi', 'detail_transaksi.id_transaksi = transaksi.id', 'left')
                    ->join('produk', 'produk.id = detail_transaksi.id_produk', 'left')
                    ->where('produk.id_supplier', $supplierFilter)
                    ->groupBy('transaksi.id');
        }

        if (!empty($tanggalMulai))   $builder->where('DATE(transaksi.tanggal_transaksi) >=', $tanggalMulai);
        if (!empty($tanggalAkhir))   $builder->where('DATE(transaksi.tanggal_transaksi) <=', $tanggalAkhir);
        if (!empty($userIdFilter))   $builder->where('transaksi.id_user', $userIdFilter);
        if (!empty($namaPembeli))    $builder->like('transaksi.nama_pembeli', $namaPembeli);

        $data = [
            'title'            => 'Riwayat Penjualan',
            'transaksi'        => $builder->paginate(10),
            'pager'            => $transaksiModel->pager,
            'users'            => $userModel->findAll(),
            'suppliers'        => $supplierModel->findAll(),
            'tanggalMulai'     => $tanggalMulai,
            'tanggalAkhir'     => $tanggalAkhir,
            'selectedUser'     => $userIdFilter,
            'selectedSupplier' => $supplierFilter,
            'namaPembeli'      => $namaPembeli,
        ];

        return view('karyawan/penjualan/index', $data);
    }

    public function create()
    {
        $produkModel = new ProdukModel();
        $pelangganModel = new PelangganModel();

        $data = [
            'title'         => 'Barang Keluar (POS)',
            'produk_list'   => $produkModel->getAvailableProducts(),
            'pelanggan_list' => $pelangganModel->findAll(),
        ];

        return view('karyawan/penjualan/create', $data);
    }

    public function store()
    {
        $transaksiModel = new TransaksiModel();
        $userId = session()->get('user_id');

        $rules = [
            'nama_pembeli'      => 'required|min_length[2]',
            'tanggal_transaksi' => 'required|valid_date',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = $this->request->getPost('items'); // array of ['id_produk', 'jumlah', 'harga_satuan']
        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Minimal satu item produk harus diisi.');
        }

        $itemsFormatted = [];
        $produkModel = new ProdukModel();
        foreach ($items as $i => $item) {
            $idProduk    = $item['id_produk'] ?? null;
            $jumlah      = $item['jumlah'] ?? 0;
            $hargaSatuan = $item['harga_satuan'] ?? 0;

            if (empty($idProduk)) {
                return redirect()->back()->withInput()->with('error', "Item ke-".($i+1).": produk harus dipilih.");
            }
            if ($jumlah <= 0) {
                return redirect()->back()->withInput()->with('error', "Item ke-".($i+1).": jumlah harus lebih dari 0.");
            }
            if ($hargaSatuan <= 0) {
                return redirect()->back()->withInput()->with('error', "Item ke-".($i+1).": harga harus lebih dari 0.");
            }

            // Cek stok
            $produk = $produkModel->find($idProduk);
            if ($produk['stok'] < $jumlah) {
                return redirect()->back()->withInput()->with('error', "Item ke-".($i+1).": stok tidak mencukupi.");
            }

            $itemsFormatted[] = [
                'id_produk'    => $idProduk,
                'jumlah'       => $jumlah,
                'harga_satuan' => $hargaSatuan
            ];
        }

        $headerData = [
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
        $detailModel    = new DetailTransaksiModel();

        $header = $transaksiModel->getDetail($id);
        if (!$header) {
            return redirect()->to('/karyawan/penjualan')->with('error', 'Transaksi tidak ditemukan.');
        }

        $items = $detailModel->getWithProductInfo($id);

        // Hitung total keseluruhan
        $totalKeseluruhan = 0;
        foreach ($items as $item) {
            $subtotal = $item['harga_satuan'] * $item['jumlah'];
            $totalKeseluruhan += $subtotal;
        }

        $data = [
            'title'  => 'Struk Penjualan #' . $header['no_invoice'],
            'header' => $header,
            'items'  => $items,
            'total'  => $totalKeseluruhan,
        ];

        return view('karyawan/penjualan/struk', $data);
    }
}