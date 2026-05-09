<?php

namespace App\Controllers\Karyawan;

use App\Controllers\BaseController;
use App\Models\PembelianModel;
use App\Models\DetailPembelianModel;
use App\Models\SupplierModel;
use App\Models\ProdukModel;
use App\Models\UserModel;

class Pembelian extends BaseController
{
    public function index()
    {
        $pembelianModel = new PembelianModel();
        $supplierModel  = new SupplierModel();
        $userModel      = new UserModel();

        $tanggalMulai  = $this->request->getGet('tanggal_mulai');
        $tanggalAkhir  = $this->request->getGet('tanggal_akhir');
        $supplierId    = $this->request->getGet('supplier');
        $userIdFilter  = $this->request->getGet('user');

        $builder = $pembelianModel
            ->select('pembelian.*, supplier.nama as supplier_nama, users.username as user_username')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->join('users', 'users.user_id = pembelian.id_user')
            ->orderBy('pembelian.id', 'DESC');

        if (!empty($tanggalMulai))   $builder->where('tanggal_pembelian >=', $tanggalMulai);
        if (!empty($tanggalAkhir))   $builder->where('tanggal_pembelian <=', $tanggalAkhir);
        if (!empty($supplierId))     $builder->where('pembelian.id_supplier', $supplierId);
        if (!empty($userIdFilter))   $builder->where('pembelian.id_user', $userIdFilter);

        $data = [
            'title'            => 'Riwayat Barang Masuk',
            'pembelian'        => $builder->paginate(10),
            'pager'            => $pembelianModel->pager,
            'suppliers'        => $supplierModel->findAll(),
            'users'            => $userModel->findAll(),
            'tanggalMulai'     => $tanggalMulai,
            'tanggalAkhir'     => $tanggalAkhir,
            'selectedSupplier' => $supplierId,
            'selectedUser'     => $userIdFilter,
        ];

        return view('karyawan/pembelian/index', $data);
    }

    public function create()
    {
        $supplierModel = new SupplierModel();
        return view('karyawan/pembelian/create', [
            'title'     => 'Tambah Barang Masuk',
            'suppliers' => $supplierModel->findAll(),
            // tidak perlu no_invoice, akan digenerate saat simpan di model
        ]);
    }

    public function store()
    {
        $pembelianModel = new PembelianModel();
        $userId = session()->get('user_id');

        $rules = [
            'id_supplier'       => 'required|is_not_unique[supplier.id]',
            'tanggal_pembelian' => 'required|valid_date',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = $this->request->getPost('items'); // array of ['id_produk', 'jumlah']
        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Minimal satu item produk harus diisi.');
        }

        // Format items & validasi basic
        $itemsFormatted = [];
        $produkModel = new ProdukModel();
        foreach ($items as $i => $item) {
            $idProduk = $item['id_produk'] ?? null;
            $jumlah   = $item['jumlah'] ?? 0;

            if (empty($idProduk)) {
                return redirect()->back()->withInput()->with('error', "Item ke-".($i+1).": produk harus dipilih.");
            }
            // Jumlah akan divalidasi di model (<=0), divalidasi juga di sini untuk umpan balik cepat
            if ($jumlah <= 0) {
                return redirect()->back()->withInput()->with('error', "Item ke-".($i+1).": jumlah harus lebih dari 0.");
            }

            $itemsFormatted[] = [
                'id_produk' => $idProduk,
                'jumlah'    => $jumlah
            ];
        }

        $headerData = [
            'id_supplier'       => $this->request->getPost('id_supplier'),
            'id_user'           => $userId,
            'tanggal_pembelian' => $this->request->getPost('tanggal_pembelian'),
            'catatan'           => $this->request->getPost('catatan'),
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
        $detailModel = new DetailPembelianModel();

        $header = $pembelianModel->getDetail($id);
        if (!$header) {
            return redirect()->to('/karyawan/pembelian')->with('error', 'Data tidak ditemukan.');
        }

        // Ambil items dari detail_pembelian, nama_produk sudah tersimpan dengan format baru
        $items = $detailModel->getByPembelianId($id);

        $data = [
            'title'  => 'Detail Barang Masuk #' . $header['no_invoice'],
            'header' => $header,
            'items'  => $items,
        ];

        return view('karyawan/pembelian/detail', $data);
    }
}