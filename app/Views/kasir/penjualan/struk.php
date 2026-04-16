<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .struk {
                width: 100%;
                margin: 0;
                padding: 10px;
            }
        }
        .struk {
            max-width: 350px;
            margin: 0 auto;
            background: white;
            font-family: monospace;
            font-size: 12px;
        }
        .struk-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }
        .struk-footer {
            text-align: center;
            border-top: 1px dashed #000;
            margin-top: 10px;
            padding-top: 10px;
        }
        .table-struk {
            width: 100%;
            font-size: 11px;
        }
        .table-struk td {
            padding: 2px 0;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="struk p-3">
        <div class="struk-header">
            <h5 class="mb-0">OMAH NINI</h5>
            <small>Jl. Contoh No. 123, Kota</small><br>
            <small>Telp: 0812-3456-7890</small>
            <hr>
            <div class="text-start">
                <div>No. Invoice: <?= $transaksi['no_invoice'] ?></div>
                <div>Tanggal: <?= date('d-m-Y H:i:s', strtotime($transaksi['tanggal_transaksi'])) ?></div>
                <div>Kasir: <?= $kasir ?></div>
                <div>Tipe Bayar: <?= strtoupper($transaksi['tipe_pembayaran']) ?></div>
            </div>
            <hr>
        </div>

        <table class="table-struk">
            <thead>
                <tr>
                    <td><strong>Item</strong></td>
                    <td class="text-end"><strong>Qty</strong></td>
                    <td class="text-end"><strong>Harga</strong></td>
                    <td class="text-end"><strong>Total</strong></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detail as $item): ?>
                <tr>
                    <td><?= $item['nama_produk'] ?></td>
                    <td class="text-end"><?= $item['jumlah'] ?></td>
                    <td class="text-end"><?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                    <td class="text-end"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                    <td class="text-end"><strong><?= number_format($total_belanja, 0, ',', '.') ?></strong></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end">Bayar</td>
                    <td class="text-end"><?= number_format($transaksi['total_bayar'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end">Kembalian</td>
                    <td class="text-end"><?= number_format($kembalian, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="struk-footer">
            <small>Terima kasih atas kunjungan Anda!</small><br>
            <small>Barang yang sudah dibeli tidak dapat dikembalikan</small>
            <hr>
            <small>*** SIMPAN STRUK INI SEBAGAI BUKTI ***</small>
        </div>

        <div class="no-print text-center mt-3">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="bi bi-printer"></i> Cetak Struk
            </button>
            <a href="<?= base_url('kasir/penjualan') ?>" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="<?= base_url('kasir/penjualan/create') ?>" class="btn btn-success btn-sm">
                <i class="bi bi-plus"></i> Transaksi Baru
            </a>
        </div>
    </div>
</body>
</html>