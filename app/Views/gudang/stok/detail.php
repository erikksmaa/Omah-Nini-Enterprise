<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-box-seam"></i> Detail Stok Produk</h5>
        </div>
        <div class="card-body p-3">
            <div class="mb-3">
                <a href="<?= base_url('gudang/stok') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="<?= base_url('gudang/stok/opname/' . $produk['id']) ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Stok Opname
                </a>
            </div>

            <!-- Informasi Produk -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Informasi Produk</h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm">
                                <tr><td width="35%">SKU</td><td>: <strong><?= $produk['sku'] ?></strong></td></tr>
                                <tr><td>Nama Produk</td><td>: <?= $produk['nama_barang'] ?></td></tr>
                                <tr><td>Kategori</td><td>: <?= $produk['nama_kategori'] ?? '-' ?></td></tr>
                                <tr><td>Supplier</td><td>: <?= $produk['nama_supplier'] ?? '-' ?></td></tr>
                                <tr><td>Harga Beli</td><td>: Rp <?= number_format($produk['harga_beli'], 0, ',', '.') ?></td></tr>
                                <tr><td>Harga Jual</td><td>: Rp <?= number_format($produk['harga_jual'], 0, ',', '.') ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Informasi Stok</h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm">
                                <tr><td width="35%">Stok Saat Ini</td>
                                    <td>: 
                                        <strong class="<?= $produk['stok'] <= $produk['min_stok'] ? 'text-danger' : 'text-success' ?>">
                                            <?= number_format($produk['stok']) ?>
                                        </strong>
                                    </td>
                                </tr>
                                <tr><td>Minimal Stok</td><td>: <?= number_format($produk['min_stok']) ?></td></tr>
                                <tr><td>Status</td>
                                    <td>: 
                                        <?php 
                                        if ($produk['stok'] <= 0) {
                                            echo '<span class="badge bg-danger">HABIS</span>';
                                        } elseif ($produk['stok'] <= $produk['min_stok']) {
                                            echo '<span class="badge bg-warning">MENIPIS</span>';
                                        } else {
                                            echo '<span class="badge bg-success">AMAN</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr><td>Nilai Stok</td>
                                    <td>: Rp <?= number_format($produk['stok'] * $produk['harga_beli'], 0, ',', '.') ?></td>
                                </tr>
                                <tr><td>Terakhir Update</td><td>: <?= $produk['updated_at'] ?? '-' ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histori Stok -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Histori Perubahan Stok</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tableHistory">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Sebelum</th>
                                    <th>Perubahan</th>
                                    <th>Sesudah</th>
                                    <th>Aktivitas</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($log_stok as $log): ?>
                                    <tr>
                                        <td><?= date('d-m-Y H:i:s', strtotime($log['created_at'])) ?></td>
                                        <td>
                                            <?php 
                                            $badge = $log['tipe_ref'] == 'pembelian' ? 'success' : ($log['tipe_ref'] == 'penjualan' ? 'primary' : 'warning');
                                            $label = $log['tipe_ref'] == 'pembelian' ? 'Masuk' : ($log['tipe_ref'] == 'penjualan' ? 'Keluar' : 'Penyesuaian');
                                            ?>
                                            <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                                        </td>
                                        <td><?= number_format($log['jumlah_sebelum']) ?></td>
                                        <td class="<?= $log['jumlah_perubahan'] > 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= $log['jumlah_perubahan'] > 0 ? '+' : '' ?><?= number_format($log['jumlah_perubahan']) ?>
                                        </td>
                                        <td><?= number_format($log['jumlah_sesudah']) ?></td>
                                        <td><?= $log['aktivitas'] ?></td>
                                        <td><?= $log['username'] ?? '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($log_stok)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada histori stok</td>
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
        $('#tableHistory').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
</script>
<?= $this->endSection() ?>