<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-truck"></i> Laporan Pembelian</h5>
        </div>
        <div class="card-body p-3">
            <!-- Filter Form -->
            <form method="GET" class="row mb-4">
                <div class="col-md-3">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?? date('Y-m-01') ?>">
                </div>
                <div class="col-md-3">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?? date('Y-m-d') ?>">
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-success w-100" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>
            </form>

            <!-- Statistik -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-white bg-primary">
                        <div class="card-body p-3">
                            <h6>Total Transaksi Pembelian</h6>
                            <h3><?= number_format($total_transaksi ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white bg-danger">
                        <div class="card-body p-3">
                            <h6>Total Pengeluaran</h6>
                            <h3>Rp <?= number_format($total_pengeluaran ?? 0, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pembelian per Supplier -->
            <?php if (!empty($pembelian_per_supplier)): ?>
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Pembelian per Supplier</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th class="text-end">Jumlah Transaksi</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pembelian_per_supplier as $item): ?>
                                    <tr>
                                        <td><?= $item['supplier_nama'] ?></td>
                                        <td class="text-end"><?= number_format($item['jumlah_transaksi']) ?>x</td>
                                        <td class="text-end">Rp <?= number_format($item['total'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Detail Pembelian -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Detail Transaksi Pembelian</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tablePembelian">
                            <thead class="table-dark">
                                <tr>
                                    <th>No Invoice</th>
                                    <th>Supplier</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Total</th>
                                    <th>Kasir</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pembelian as $item): ?>
                                    <tr>
                                        <td><strong><?= $item['no_invoice'] ?></strong></td>
                                        <td><?= $item['supplier_nama'] ?></td>
                                        <td><?= date('d-m-Y', strtotime($item['tanggal_pembelian'])) ?></td>
                                        <td class="text-end">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                                        <td><?= $item['username'] ?? '-' ?></td>
                                        <td><?= $item['catatan'] ?? '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($pembelian)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data pembelian</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tablePembelian').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[2, 'desc']],
            pageLength: 25
        });
    });
</script>
<?= $this->endSection() ?>