<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Detail Stok Produk</h4>
        <div>
            <a href="<?= base_url('karyawan/stok/opname/' . $produk['id']) ?>" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Opname
            </a>
            <a href="<?= base_url('karyawan/stok') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table  table-borderless">
                    <tr><th width="150">SKU</th><td>: <?= esc($produk['sku']) ?></td></tr>
                    <tr><th>Merk</th><td>: <?= esc($produk['nama_supplier'] ?? $produk['nama'] ?? '-') ?></td></tr>
                    <tr><th>Motif</th><td>: <?= esc($produk['nama_motif']) ?></td></tr>
                    <tr><th>Warna</th><td>: <?= esc($produk['nama_warna']) ?></td></tr>
                    <tr><th>Stok Saat Ini</th><td>: <strong><?= $produk['stok'] ?></strong> potong</td></tr>
                    <tr><th>Stok Minimal</th><td>: <?= $produk['min_stok'] ?> potong</td></tr>
                    <tr><th>Keterangan</th><td>: <?= esc($produk['keterangan'] ?? '-') ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <?php
                $stok = $produk['stok'];
                $min  = $produk['min_stok'];
                if ($stok == 0): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Stok habis! Segera lakukan pembelian.
                    </div>
                <?php elseif ($stok <= $min): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Stok menipis! Tersedia <?= $stok ?> dari minimal <?= $min ?>.
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Stok aman.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Stok Produk -->
<div class="card">
    <div class="card-header">
        <h5>Riwayat Perubahan Stok</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tgl / Waktu</th>
                        <th>Tipe</th>
                        <th>Sebelum</th>
                        <th>Perubahan</th>
                        <th>Sesudah</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($log_stok)): ?>
                    <?php foreach ($log_stok as $log): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                            <td>
                                <?php
                                $badge = 'secondary';
                                if ($log['tipe_ref'] == 'pembelian') $badge = 'success';
                                elseif ($log['tipe_ref'] == 'penjualan') $badge = 'danger';
                                elseif ($log['tipe_ref'] == 'penyesuaian') $badge = 'warning';
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst($log['tipe_ref']) ?></span>
                            </td>
                            <td><?= $log['jumlah_sebelum'] ?></td>
                            <td>
                                <span class="<?= $log['jumlah_perubahan'] > 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $log['jumlah_perubahan'] > 0 ? '+' . $log['jumlah_perubahan'] : $log['jumlah_perubahan'] ?>
                                </span>
                            </td>
                            <td><?= $log['jumlah_sesudah'] ?></td>
                            <td><?= esc($log['username']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada riwayat perubahan stok.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?>
    </div>
</div>

<?= $this->endSection() ?>