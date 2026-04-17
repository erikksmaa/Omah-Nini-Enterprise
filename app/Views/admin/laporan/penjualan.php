<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-receipt"></i> Laporan Penjualan</h5>
        </div>
        <div class="card-body p-3">
            <!-- Filter Form -->
            <form method="GET" class="row mb-4">
                <div class="col-md-3">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                <div class="col-md-3">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
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
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <a href="<?= base_url('admin/laporan/export-penjualan?start_date=' . $start_date . '&end_date=' . $end_date) ?>" class="btn btn-primary w-100">
                        <i class="bi bi-file-excel"></i> Export
                    </a>
                </div>
            </form>

            <!-- Statistik -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body p-3">
                            <h6>Total Transaksi</h6>
                            <h3><?= number_format($total_transaksi) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body p-3">
                            <h6>Total Omset</h6>
                            <h3>Rp <?= number_format($total_omset, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body p-3">
                            <h6>Total Item Terjual</h6>
                            <h3><?= number_format($total_item_terjual) ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penjualan per Hari -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Penjualan per Hari</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Tanggal</th><th>Jumlah Transaksi</th><th>Total</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($penjualan_per_hari as $item): ?>
                                    <tr>
                                        <td><?= date('d-m-Y', strtotime($item['tanggal'])) ?></td>
                                        <td><?= number_format($item['jumlah_transaksi']) ?>x</td>
                                        <td>Rp <?= number_format($item['total'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Produk Terlaris -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">Top 10 Produk Terlaris</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Produk</th><th>Terjual</th><th>Omset</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produk_terlaris as $item): ?>
                                    <tr>
                                        <td><?= $item['nama_produk'] ?></td>
                                        <td><?= number_format($item['total_terjual']) ?> pcs</td>
                                        <td>Rp <?= number_format($item['total_omset'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detail Transaksi -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Detail Transaksi</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tablePenjualan">
                            <thead class="table-dark">
                                <tr>
                                    <th>No Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Total Bayar</th>
                                    <th>Tipe Bayar</th>
                                    <th>Kasir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($penjualan as $item): ?>
                                    <tr>
                                        <td><?= $item['no_invoice'] ?></td>
                                        <td><?= date('d-m-Y H:i', strtotime($item['created_at'])) ?></td>
                                        <td>Rp <?= number_format($item['total_bayar'], 0, ',', '.') ?></td>
                                        <td><?= strtoupper($item['tipe_pembayaran']) ?></td>
                                        <td><?= $item['username'] ?? '-' ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="showStruk(<?= $item['id'] ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
        $('#tablePenjualan').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[1, 'desc']],
            pageLength: 25
        });
    });
    
    function showStruk(id) {
        window.open(`<?= base_url('kasir/penjualan/struk/') ?>${id}`, '_blank');
    }
</script>
<?= $this->endSection() ?>