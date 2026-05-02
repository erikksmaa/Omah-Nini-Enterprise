<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h4><?= $title ?></h4>
    </div>
    <div class="card-body">

        <!-- Filter -->
        <form method="get" class="row mb-4">
            <div class="col-md-5">
                <label class="form-label">Produk</label>
                <select name="produk" class="form-select">
                    <option value="">-- Semua Produk --</option>
                    <?php foreach ($produk_list as $prod): ?>
                        <option value="<?= $prod['id'] ?>" <?= $selectedProduk == $prod['id'] ? 'selected' : '' ?>>
                            <?= esc($prod['sku'] . ' - ' . $prod['nama_motif'] . ' ' . $prod['nama_warna']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipe Perubahan</label>
                <select name="tipe" class="form-select">
                    <option value="">-- Semua Tipe --</option>
                    <option value="pembelian" <?= $selectedTipe == 'pembelian' ? 'selected' : '' ?>>Pembelian (Masuk)</option>
                    <option value="penjualan" <?= $selectedTipe == 'penjualan' ? 'selected' : '' ?>>Penjualan (Keluar)</option>
                    <option value="penyesuaian" <?= $selectedTipe == 'penyesuaian' ? 'selected' : '' ?>>Penyesuaian (Opname)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="<?= base_url('karyawan/stok/history') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <!-- Tabel Log -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>SKU</th>
                        <th>Produk</th>
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
                            <td><?= esc($log['sku']) ?></td>
                            <td><?= esc($log['nama_motif'] . ' ' . $log['nama_warna']) ?></td>
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
                        <td colspan="8" class="text-center">Belum ada riwayat perubahan stok.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?>
    </div>
</div>

<?= $this->endSection() ?>