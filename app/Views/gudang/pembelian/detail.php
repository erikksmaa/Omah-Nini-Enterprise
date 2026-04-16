<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-receipt"></i> Detail Pembelian</h5>
            <div>
                <a href="<?= base_url('gudang/pembelian') ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button onclick="window.print()" class="btn btn-light btn-sm">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
        </div>
        <div class="card-body p-3">
            <!-- Informasi Pembelian -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="border p-3 rounded">
                        <h6 class="text-primary mb-3"><i class="bi bi-info-circle"></i> Informasi Pembelian</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="120">No. Invoice</td>
                                <td>: <strong><?= $pembelian['no_invoice'] ?></strong></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td>: <?= date('d-m-Y', strtotime($pembelian['tanggal_pembelian'])) ?></td>
                            </tr>
                            <tr>
                                <td>Supplier</td>
                                <td>: <?= $pembelian['supplier_nama'] ?></td>
                            </tr>
                            <tr>
                                <td>User</td>
                                <td>: <?= $pembelian['username'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <td>Catatan</td>
                                <td>: <?= $pembelian['catatan'] ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border p-3 rounded">
                        <h6 class="text-primary mb-3"><i class="bi bi-calculator"></i> Ringkasan</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="120">Total Item</td>
                                <td>: <?= count($detail) ?> produk</td>
                            </tr>
                            <tr>
                                <td>Total Jumlah</td>
                                <td>: 
                                    <?php 
                                    $totalJumlah = array_sum(array_column($detail, 'jumlah'));
                                    echo number_format($totalJumlah);
                                    ?> pcs
                                </td>
                            </tr>
                            <tr>
                                <td>Total Harga</td>
                                <td>: <strong class="text-success">Rp <?= number_format($pembelian['total_harga'], 0, ',', '.') ?></strong></td>
                            </tr>
                            <tr>
                                <td>Waktu Input</td>
                                <td>: <?= date('d-m-Y H:i:s', strtotime($pembelian['created_at'])) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Daftar Produk -->
            <div class="border rounded">
                <div class="bg-secondary text-white p-2 rounded-top">
                    <h6 class="mb-0"><i class="bi bi-box"></i> Daftar Produk</h6>
                </div>
                <div class="p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th class="text-end">Harga Beli</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($detail)): ?>
                                    <?php $no = 1; foreach ($detail as $item): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $item['nama_produk'] ?></td>
                                            <td class="text-end">Rp <?= number_format($item['harga_beli'], 0, ',', '.') ?></td>
                                            <td class="text-center"><?= number_format($item['jumlah']) ?></td>
                                            <td class="text-end">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data produk</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">TOTAL</td>
                                    <td class="text-end fw-bold">Rp <?= number_format($pembelian['total_harga'], 0, ',', '.') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style media="print">
    .btn, .card-header .btn, .sidebar, nav, footer {
        display: none !important;
    }
    .card {
        border: none !important;
    }
    body {
        margin: 0;
        padding: 0;
    }
</style>
<?= $this->endSection() ?>