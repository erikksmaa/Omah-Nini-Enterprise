<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <!-- Statistik -->
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h6>Total Produk</h6>
                    <h3><?= number_format($total_produk ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h6>Total Nilai Stok</h6>
                    <h4>Rp <?= number_format($total_nilai_stok ?? 0, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk Stok Terbanyak -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-arrow-up"></i> Top 10 Produk Stok Terbanyak</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th class="text-end">Stok</th>
                            <th class="text-end">Nilai Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stok_terbanyak as $item): ?>
                            <tr>
                                <td><?= $item['sku'] ?></td>
                                <td><strong><?= $item['nama_barang'] ?></strong></td>
                                <td><?= $item['kategori_nama'] ?? '-' ?></td>
                                <td class="text-end"><?= number_format($item['stok']) ?></td>
                                <td class="text-end">Rp <?= number_format($item['stok'] * $item['harga_beli'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($stok_terbanyak)): ?>
                            <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Produk Stok Menipis -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Produk Stok Menipis</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th class="text-end">Stok</th>
                            <th class="text-end">Min Stok</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stok_menipis as $item): ?>
                            <tr>
                                <td><?= $item['sku'] ?></td>
                                <td><strong><?= $item['nama_barang'] ?></strong></td>
                                <td><?= $item['kategori_nama'] ?? '-' ?></td>
                                <td class="text-end text-danger fw-bold"><?= number_format($item['stok']) ?></td>
                                <td class="text-end"><?= number_format($item['min_stok']) ?></td>
                                <td class="text-end">
                                    <?php if ($item['stok'] <= 0): ?>
                                        <span class="badge bg-danger">HABIS</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">MENIPIS</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($stok_menipis)): ?>
                            <tr><td colspan="6" class="text-center">Semua stok aman</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Produk Terlaris -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-trophy"></i> Top 10 Produk Terlaris</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th class="text-end">Terjual</th>
                            <th class="text-end">Omset</th>
                            <th>Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalOmset = array_sum(array_column($produk_terlaris, 'total_omset'));
                        $no = 1;
                        foreach ($produk_terlaris as $item): 
                            $persen = $totalOmset > 0 ? ($item['total_omset'] / $totalOmset) * 100 : 0;
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $item['nama_produk'] ?></strong></td>
                                <td class="text-end"><?= number_format($item['total_terjual']) ?> pcs</td>
                                <td class="text-end">Rp <?= number_format($item['total_omset'], 0, ',', '.') ?></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: <?= $persen ?>%">
                                            <?= number_format($persen, 1) ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($produk_terlaris)): ?>
                            <tr><td colspan="5" class="text-center">Belum ada data penjualan</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total Omset</td>
                            <td class="text-end fw-bold">Rp <?= number_format($totalOmset, 0, ',', '.') ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Produk Tidak Pernah Terjual -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="bi bi-question-circle"></i> Produk Tidak Pernah Terjual</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="tableNeverSold">
                    <thead class="table-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th class="text-end">Stok</th>
                            <th class="text-end">Nilai Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produk_never_sold as $item): ?>
                            <tr>
                                <td><?= $item['sku'] ?></td>
                                <td><?= $item['nama_barang'] ?></td>
                                <td class="text-end"><?= number_format($item['stok']) ?></td>
                                <td class="text-end">Rp <?= number_format($item['stok'] * $item['harga_beli'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($produk_never_sold)): ?>
                            <tr><td colspan="4" class="text-center">Semua produk sudah pernah terjual</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tableNeverSold').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            pageLength: 10
        });
    });
</script>
<?= $this->endSection() ?>