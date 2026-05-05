<?php

namespace App\Controllers\Pemilik;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\LogStokModel;
use App\Models\PembelianModel;
use App\Models\TransaksiModel;
use App\Models\SupplierModel;
use App\Models\MotifModel;
use App\Models\WarnaModel;
use App\Models\DetailPembelianModel;

use App\Models\DetailTransaksiModel;
use App\Models\PelangganModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class Laporan extends BaseController
{
    protected $produkModel;
    protected $logStokModel;
    protected $pembelianModel;
    protected $transaksiModel;
    protected $supplierModel;
    protected $motifModel;
    protected $warnaModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->logStokModel = new LogStokModel();
        $this->pembelianModel = new PembelianModel();
        $this->transaksiModel = new TransaksiModel();
        $this->supplierModel = new SupplierModel();
        $this->motifModel = new MotifModel();
        $this->warnaModel = new WarnaModel();
    }

    // ========== LAPORAN STOK ==========
    public function stok()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $keyword = $this->request->getGet('keyword');
        $filter_supplier = $this->request->getGet('filter_supplier');
        $filter_motif = $this->request->getGet('filter_motif');
        $filter_warna = $this->request->getGet('filter_warna');
        $filter_stok = $this->request->getGet('filter_stok');

        // Default: 30 hari terakhir jika kosong
        if (empty($start_date) && empty($end_date)) {
            $start_date = date('Y-m-d', strtotime('-30 days'));
            $end_date = date('Y-m-d');
        }

        $data = [
            'title' => 'Laporan Stok Produk',
            'produk' => $this->produkModel->getStockReportWithFilters(
                $start_date,
                $end_date,
                $keyword,
                $filter_supplier,
                $filter_motif,
                $filter_warna,
                $filter_stok
            ),
            'summary' => $this->produkModel->getStockSummary(),
            'suppliers' => $this->supplierModel->getOptions(),
            'motifs' => $this->motifModel->getOptions(),
            'warnas' => $this->warnaModel->getOptions(),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'keyword' => $keyword,
            'filter_supplier' => $filter_supplier,
            'filter_motif' => $filter_motif,
            'filter_warna' => $filter_warna,
            'filter_stok' => $filter_stok,
        ];

        return view('pemilik/laporan/stok', $data);
    }

    // ========== LAPORAN LOG STOK ==========
    public function logStok()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $id_produk = $this->request->getGet('id_produk');
        $tipe_ref = $this->request->getGet('tipe_ref');

        // Default tanggal: 30 hari terakhir
        if (empty($start_date)) {
            $start_date = date('Y-m-d', strtotime('-30 days'));
        }
        if (empty($end_date)) {
            $end_date = date('Y-m-d');
        }

        $data = [
            'title' => 'Laporan Log Stok (Histori Perubahan Stok)',
            'logs' => $this->logStokModel->getLogStokReport($start_date, $end_date, $id_produk, $tipe_ref),
            'summary' => $this->logStokModel->getSummaryPerTipe($start_date, $end_date),
            'produk_list' => $this->produkModel->getOptions(),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'filter_produk' => $id_produk,
            'filter_tipe' => $tipe_ref,
        ];

        return view('pemilik/laporan/log_stok', $data);
    }

    public function exportStokExcel()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $keyword = $this->request->getGet('keyword');
        $filter_supplier = $this->request->getGet('filter_supplier');
        $filter_motif = $this->request->getGet('filter_motif');
        $filter_warna = $this->request->getGet('filter_warna');
        $filter_stok = $this->request->getGet('filter_stok');

        // Ambil data sesuai filter (sama dengan tampilan)
        $data = $this->produkModel->getStockReportWithFilters(
            $start_date,
            $end_date,
            $keyword,
            $filter_supplier,
            $filter_motif,
            $filter_warna,
            $filter_stok
        );
        $summary = $this->produkModel->getStockSummary();

        // Nama supplier, motif, warna untuk ditampilkan di filter
        $supplier_nama = '';
        if ($filter_supplier) {
            $sup = $this->supplierModel->find($filter_supplier);
            $supplier_nama = $sup['nama'] ?? '';
        }
        $motif_nama = '';
        if ($filter_motif) {
            $mot = $this->motifModel->find($filter_motif);
            $motif_nama = $mot['nama_motif'] ?? '';
        }
        $warna_nama = '';
        if ($filter_warna) {
            $wr = $this->warnaModel->find($filter_warna);
            $warna_nama = $wr['nama_warna'] ?? '';
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // === JUDUL ===
        $sheet->setCellValue('A1', 'LAPORAN STOK PRODUK');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // === INFO FILTER ===
        $row = 3;
        $sheet->setCellValue("A{$row}", 'Periode:');
        $sheet->setCellValue("B{$row}", (!empty($start_date) ? date('d/m/Y', strtotime($start_date)) : 'Semua') . ' s.d ' . (!empty($end_date) ? date('d/m/Y', strtotime($end_date)) : 'Semua'));
        $sheet->mergeCells("B{$row}:I{$row}");
        $row++;

        if (!empty($keyword)) {
            $sheet->setCellValue("A{$row}", 'Keyword:');
            $sheet->setCellValue("B{$row}", $keyword);
            $sheet->mergeCells("B{$row}:I{$row}");
            $row++;
        }
        if (!empty($filter_supplier)) {
            $sheet->setCellValue("A{$row}", 'Supplier:');
            $sheet->setCellValue("B{$row}", $supplier_nama);
            $sheet->mergeCells("B{$row}:I{$row}");
            $row++;
        }
        if (!empty($filter_motif)) {
            $sheet->setCellValue("A{$row}", 'Motif:');
            $sheet->setCellValue("B{$row}", $motif_nama);
            $sheet->mergeCells("B{$row}:I{$row}");
            $row++;
        }
        if (!empty($filter_warna)) {
            $sheet->setCellValue("A{$row}", 'Warna:');
            $sheet->setCellValue("B{$row}", $warna_nama);
            $sheet->mergeCells("B{$row}:I{$row}");
            $row++;
        }
        if (!empty($filter_stok)) {
            $stok_text = '';
            if ($filter_stok == 'aman')
                $stok_text = 'Aman (Stok > Minimal)';
            elseif ($filter_stok == 'menipis')
                $stok_text = 'Menipis (Stok ≤ Minimal, >0)';
            elseif ($filter_stok == 'habis')
                $stok_text = 'Habis (Stok = 0)';
            $sheet->setCellValue("A{$row}", 'Status Stok:');
            $sheet->setCellValue("B{$row}", $stok_text);
            $sheet->mergeCells("B{$row}:I{$row}");
            $row++;
        }
        $row++;

        // === RINGKASAN ===
        $sheet->setCellValue("A{$row}", 'RINGKASAN STOK');
        $sheet->mergeCells("A{$row}:I{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("A{$row}", 'Total Produk');
        $sheet->setCellValue("B{$row}", $summary['total_produk'] ?? 0);
        $sheet->setCellValue("C{$row}", 'Total Stok');
        $sheet->setCellValue("D{$row}", $summary['total_stok'] ?? 0);
        $sheet->setCellValue("E{$row}", 'Stok Menipis');
        $sheet->setCellValue("F{$row}", $summary['produk_menipis'] ?? 0);
        $sheet->setCellValue("G{$row}", 'Stok Habis');
        $sheet->setCellValue("H{$row}", $summary['produk_habis'] ?? 0);
        $row += 2;

        // === HEADER TABEL ===
        $headers = ['No', 'SKU', 'Supplier', 'Motif', 'Warna', 'Stok', 'Min Stok', 'Status', 'Update Terakhir'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
            $col++;
        }
        $row++;

        // === DATA ===
        $no = 1;
        foreach ($data as $item) {
            $col = 'A';
            $sheet->setCellValue($col++ . $row, $no++);
            $sheet->setCellValue($col++ . $row, $item['sku']);
            $sheet->setCellValue($col++ . $row, $item['nama_supplier']);
            $sheet->setCellValue($col++ . $row, $item['nama_motif']);
            $sheet->setCellValue($col++ . $row, $item['nama_warna']);
            $sheet->setCellValue($col++ . $row, $item['stok']);
            $sheet->setCellValue($col++ . $row, $item['min_stok']);

            // Status
            $status = '';
            if ($item['stok'] == 0)
                $status = 'Habis';
            elseif ($item['stok'] <= $item['min_stok'])
                $status = 'Menipis';
            else
                $status = 'Aman';
            $sheet->setCellValue($col++ . $row, $status);

            $sheet->setCellValue($col++ . $row, date('d/m/Y H:i', strtotime($item['updated_at'])));
            $row++;
        }

        // === AUTO WIDTH ===
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border untuk tabel
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A3:' . $col . $lastRow)->applyFromArray($styleArray);

        // Output file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Stok_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    /**
     * Export Log Stok ke Excel
     */
    public function exportLogStokExcel()
    {
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $id_produk = $this->request->getGet('id_produk');
        $tipe_ref = $this->request->getGet('tipe_ref');

        // Ambil data log stok sesuai filter
        $logs = $this->logStokModel->getLogStokReport($start_date, $end_date, $id_produk, $tipe_ref);
        $summary = $this->logStokModel->getSummaryPerTipe($start_date, $end_date);

        // Nama produk untuk filter
        $produk_nama = '';
        if ($id_produk) {
            $p = $this->produkModel->find($id_produk);
            if ($p) {
                $motif = $this->motifModel->find($p['id_motif']);
                $warna = $this->warnaModel->find($p['id_warna']);
                $produk_nama = ($motif['nama_motif'] ?? '') . ' - ' . ($warna['nama_warna'] ?? '');
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // === JUDUL ===
        $sheet->setCellValue('A1', 'LAPORAN LOG STOK (HISTORI PERUBAHAN STOK)');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // === INFO FILTER ===
        $row = 3;
        $sheet->setCellValue("A{$row}", 'Periode:');
        $sheet->setCellValue("B{$row}", (!empty($start_date) ? date('d/m/Y', strtotime($start_date)) : 'Semua') . ' s.d ' . (!empty($end_date) ? date('d/m/Y', strtotime($end_date)) : 'Semua'));
        $sheet->mergeCells("B{$row}:H{$row}");
        $row++;

        if (!empty($id_produk)) {
            $sheet->setCellValue("A{$row}", 'Produk:');
            $sheet->setCellValue("B{$row}", $produk_nama);
            $sheet->mergeCells("B{$row}:H{$row}");
            $row++;
        }
        if (!empty($tipe_ref)) {
            $tipe_text = '';
            if ($tipe_ref == 'pembelian')
                $tipe_text = 'Pembelian (Stok +)';
            elseif ($tipe_ref == 'penjualan')
                $tipe_text = 'Penjualan (Stok -)';
            elseif ($tipe_ref == 'penyesuaian')
                $tipe_text = 'Penyesuaian (Opname)';
            $sheet->setCellValue("A{$row}", 'Tipe Transaksi:');
            $sheet->setCellValue("B{$row}", $tipe_text);
            $sheet->mergeCells("B{$row}:H{$row}");
            $row++;
        }
        $row++;

        // === RINGKASAN PER TIPE ===
        $sheet->setCellValue("A{$row}", 'RINGKASAN PERUBAHAN STOK');
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("A{$row}", 'Tipe');
        $sheet->setCellValue("B{$row}", 'Total Perubahan');
        $sheet->setCellValue("C{$row}", 'Jumlah Transaksi');
        $sheet->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);

        $summaryRow = $row + 1;
        foreach ($summary as $s) {
            $sheet->setCellValue("A{$summaryRow}", $s['tipe_ref']);
            $sheet->setCellValue("B{$summaryRow}", $s['total_perubahan']);
            $sheet->setCellValue("C{$summaryRow}", $s['jumlah_transaksi']);
            $summaryRow++;
        }
        $row = $summaryRow + 1;

        // === HEADER TABEL LOG ===
        $headers = ['No', 'Tanggal', 'User', 'Produk', 'SKU', 'Tipe', 'Stok Sebelum', 'Perubahan', 'Stok Sesudah'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
            $col++;
        }
        $row++;

        // === DATA LOG ===
        $no = 1;
        foreach ($logs as $log) {
            $col = 'A';
            $sheet->setCellValue($col++ . $row, $no++);
            $sheet->setCellValue($col++ . $row, date('d/m/Y H:i', strtotime($log['created_at'])));
            $sheet->setCellValue($col++ . $row, $log['username']);

            $nama_produk = $log['nama_motif'] . ' - ' . $log['nama_warna'];
            $sheet->setCellValue($col++ . $row, $nama_produk);
            $sheet->setCellValue($col++ . $row, $log['sku']);
            $sheet->setCellValue($col++ . $row, $log['tipe_ref']);
            $sheet->setCellValue($col++ . $row, $log['jumlah_sebelum']);
            $sheet->setCellValue($col++ . $row, $log['jumlah_perubahan']);
            $sheet->setCellValue($col++ . $row, $log['jumlah_sesudah']);
            $row++;
        }

        // Auto width
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border untuk tabel
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A3:' . $col . $lastRow)->applyFromArray($styleArray);

        // Output file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Log_Stok_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function barangMasuk()
    {
        $pembelianModel = new PembelianModel();
        $supplierModel = new SupplierModel();
        $detailModel = new DetailPembelianModel();

        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');
        $supplierId = $this->request->getGet('supplier');

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

        foreach ($pembelian as &$pemb) {
            $items = $detailModel->where('id_pembelian', $pemb['id'])->findAll();
            $pemb['items'] = $items;           // Simpan items ke array
            $pemb['total_items'] = count($items); // Simpan total items
        }

        $data = [
            'title' => 'Laporan Barang Masuk',
            'pembelian' => $pembelian,
            'pager' => $pembelianModel->pager,
            'suppliers' => $supplierModel->findAll(),
            'selectedSupplier' => $supplierId,
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir,
        ];

        return view('pemilik/laporan/barang-masuk', $data);
    }

    public function barangKeluar()
    {
        $transaksiModel = new TransaksiModel();
        $pelangganModel = new PelangganModel();
        $detailModel = new DetailTransaksiModel();

        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');
        $pelangganId = $this->request->getGet('pelanggan');

        $builder = $transaksiModel
            ->select('transaksi.*, pelanggan.nama as nama_pelanggan')
            ->join('pelanggan', 'pelanggan.id = transaksi.id_pelanggan', 'left');

        if (!empty($tanggalMulai)) {
            $builder->where('DATE(tanggal_transaksi) >=', $tanggalMulai);
        }
        if (!empty($tanggalAkhir)) {
            $builder->where('DATE(tanggal_transaksi) <=', $tanggalAkhir);
        }
        if (!empty($pelangganId)) {
            $builder->where('transaksi.id_pelanggan', $pelangganId);
        }

        $transaksi = $builder->orderBy('transaksi.id', 'DESC')->paginate(15);

        foreach ($transaksi as &$trans) {
            $items = $detailModel->where('id_transaksi', $trans['id'])->findAll();
            $trans['items'] = $items;           // Simpan items ke array
            $trans['total_items'] = count($items); // Simpan total items
        }

        $data = [
            'title' => 'Laporan Barang Keluar',
            'transaksi' => $transaksi,
            'pager' => $transaksiModel->pager,
            'pelanggan_list' => $pelangganModel->findAll(),
            'selectedPelanggan' => $pelangganId,
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir,
        ];

        return view('pemilik/laporan/barang-keluar', $data);
    }
}