<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-arrow-return-left"></i> Detail Retur Penjualan</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="<?= base_url('admin/retur') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Informasi Retur</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr><td width="35%">No Retur</td><td>: <strong><?= $retur['no_retur'] ?></strong></td></tr>
                                <tr><td>No Invoice</td><td>: <?= $retur['no_invoice'] ?></td></tr>
                                <tr><td>Tanggal Retur</td><td>: <?= date('d-m-Y', strtotime($retur['tanggal_retur'])) ?></td></tr>
                                <tr><td>Total Retur</td><td>: <strong class="text-danger">Rp <?= number_format($retur['total_retur'], 0, ',', '.') ?></strong></td></tr>
                                <tr><td>Alasan</td><td>: <?= $retur['alasan'] ?></td></tr>
                                <tr><td>User</td><td>: <?= $retur['username'] ?? '-' ?></td></tr>
                                <tr><td>Tanggal Input</td><td>: <?= date('d-m-Y H:i:s', strtotime($retur['created_at'])) ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Produk yang Diretur</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th class="text-end">Harga Jual</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($detail as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $item['nama_produk'] ?></td>
                                    <td class="text-end">Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?></td>
                                    <td class="text-end"><?= number_format($item['jumlah']) ?></td>
                                    <td class="text-end">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">TOTAL RETUR</td>
                                    <td class="text-end fw-bold">Rp <?= number_format($retur['total_retur'], 0, ',', '.') ?></td>
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
    .btn, .sidebar, nav, footer {
        display: none !important;
    }
    .card {
        border: none !important;
    }
</style>
<?= $this->endSection() ?>