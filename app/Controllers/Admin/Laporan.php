<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PembelianModel;
use App\Models\SupplierModel;
use App\Models\DetailPembelianModel;

class Laporan extends BaseController
{
    public function barangMasuk()
    {
        $pembelianModel = new PembelianModel();
        $supplierModel  = new SupplierModel();
        $detailModel    = new DetailPembelianModel();

        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');
        $supplierId   = $this->request->getGet('supplier');

        $builder = $pembelianModel
            ->select('pembelian.*, supplier.nama as nama_supplier')
            ->join('supplier', 'supplier.id = pembelian.id_supplier');

        if (!empty($tanggalMulai)) {
            $builder->where('tanggal_pembelian >=', $tanggalMulai);
        }
        if (!empty($tanggalAkhir)) {
            $builder->where('tanggal_pembelian <=', $tanggalAkhir);
        }
        if (!empty($supplierId)) {
            $builder->where('pembelian.id_supplier', $supplierId);
        }

        $pembelian = $builder->orderBy('pembelian.id', 'DESC')->paginate(15);

        $data = [
            'title'           => 'Laporan Barang Masuk',
            'pembelian'       => $pembelian,
            'pager'           => $pembelianModel->pager,
            'suppliers'       => $supplierModel->findAll(),
            'selectedSupplier'=> $supplierId,
            'tanggalMulai'    => $tanggalMulai,
            'tanggalAkhir'    => $tanggalAkhir,
        ];

        return view('admin/laporan/barang-masuk', $data);
    }

}