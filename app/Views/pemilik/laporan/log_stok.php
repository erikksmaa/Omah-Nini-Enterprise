<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
                </div>
                <div class="card-body p-2 p-md-3">

                    <!-- Filter Form -->
                    <div class="card mb-3">
                        <div class="card-body p-2 p-md-3">
                            <form method="GET" class="row g-2">
                                <div class="col-md-3 col-6">
                                    <input type="date" name="start_date" class="form-control form-control-sm"
                                        value="<?= $start_date ?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input type="date" name="end_date" class="form-control form-control-sm"
                                        value="<?= $end_date ?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <select name="id_produk" class="form-select form-select-sm">
                                        <option value="">Semua Produk</option>
                                        <?php foreach ($produk_list as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= ($filter_produk ?? '') == $p['id'] ? 'selected' : '' ?>>
                                                <?= esc($p['nama_motif'] ?? $p['nama_produk']) ?> -
                                                <?= esc($p['nama_warna'] ?? '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <select name="tipe_ref" class="form-select form-select-sm">
                                        <option value="">Semua Tipe</option>
                                        <option value="pembelian" <?= ($filter_tipe ?? '') == 'pembelian' ? 'selected' : '' ?>>Pembelian (Stok +)</option>
                                        <option value="penjualan" <?= ($filter_tipe ?? '') == 'penjualan' ? 'selected' : '' ?>>Penjualan (Stok -)</option>
                                        <option value="penyesuaian" <?= ($filter_tipe ?? '') == 'penyesuaian' ? 'selected' : '' ?>>Penyesuaian (Opname)</option>
                                    </select>
                                </div>
                                <div class="col-12 text-end mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                    <a href="<?= base_url('pemilik/laporan/log-stok') ?>"
                                        class="btn btn-secondary btn-sm">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tombol Export -->
                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= base_url('pemilik/laporan/export-log-stok?' . http_build_query($_GET)) ?>"
                            class="btn btn-success btn-sm">
                            <i class="bi bi-file-excel"></i> Export Excel
                        </a>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-0">
                        <div class="col-md-6 col-6 mb-2">
                            <div class="card bg-success text-white">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">Pembelian (Stok +)</h6>
                                    <?php $total_beli = 0;
                                    foreach ($summary as $s):
                                        if ($s['tipe_ref'] == 'pembelian')
                                            $total_beli = $s['total_perubahan'];
                                    endforeach; ?>
                                    <h5 class="mb-0">+ <?= number_format($total_beli) ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">Penjualan (Stok -)</h6>
                                    <?php $total_jual = 0;
                                    foreach ($summary as $s):
                                        if ($s['tipe_ref'] == 'penjualan')
                                            $total_jual = abs($s['total_perubahan']);
                                    endforeach; ?>
                                    <h5 class="mb-0">- <?= number_format($total_jual) ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========== TABEL (Desktop) ========== -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    </tr>
                                    <th>Tanggal</th>
                                    <th>User</th>
                                    <th>Produk</th>
                                    <th>Tipe</th>
                                    <th class="text-center">Stok Sebelum</th>
                                    <th class="text-center">Perubahan</th>
                                    <th class="text-center">Stok Sesudah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($logs)): ?>
                                        <?php foreach ($logs as $log): ?>
                                            <?php
                                            $badgeClass = '';
                                            if ($log['tipe_ref'] == 'pembelian')
                                                $badgeClass = 'bg-success';
                                            elseif ($log['tipe_ref'] == 'penjualan')
                                                $badgeClass = 'bg-danger';
                                            else
                                                $badgeClass = 'bg-info';
                                            ?>
                                            <tr>
                                                <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                                                <td><?= esc($log['username']) ?></td>
                                                <td><?= esc($log['nama_motif']) ?> - <?= esc($log['nama_warna']) ?></td>
                                                <td><span class="badge <?= $badgeClass ?>"><?= $log['tipe_ref'] ?></span></td>
                                                <td class="text-center"><?= number_format($log['jumlah_sebelum']) ?></td>
                                                <td
                                                    class="text-center <?= str_contains((string) $log['jumlah_perubahan'], '-') || $log['tipe_ref'] == 'penjualan' ? 'text-danger' : 'text-success' ?>">
                                                    <?php if ($log['tipe_ref'] == 'pembelian'): ?>
                                                        + <?= number_format($log['jumlah_perubahan']) ?>
                                                    <?php elseif ($log['tipe_ref'] == 'penjualan'): ?>
                                                        - <?= number_format(abs($log['jumlah_perubahan'])) ?>
                                                    <?php else: ?>
                                                        <?= $log['jumlah_perubahan'] > 0 ? '+' : '' ?>
                                                        <?= number_format($log['jumlah_perubahan']) ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= number_format($log['jumlah_sesudah']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data log stok</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <?php if (isset($pager) && $pager): ?>
                                <?= $pager->links('default', 'bootstrap_pagination') ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ========== CARD VIEW (Mobile) ========== -->
                    <div class="d-md-none">
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <?php
                                $badgeClass = '';
                                if ($log['tipe_ref'] == 'pembelian')
                                    $badgeClass = 'bg-success';
                                elseif ($log['tipe_ref'] == 'penjualan')
                                    $badgeClass = 'bg-danger';
                                else
                                    $badgeClass = 'bg-info';

                                $perubahanClass = $log['jumlah_perubahan'] > 0 ? 'text-success' : 'text-danger';
                                $perubahanSign = $log['jumlah_perubahan'] > 0 ? '+' : '';
                                ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body p-2">
                                        <!-- Header Card -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <small
                                                    class="text-muted"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></small>
                                                <span class="badge <?= $badgeClass ?> ms-2"><?= $log['tipe_ref'] ?></span>
                                            </div>
                                            <div>
                                                <i class="bi bi-person-circle text-muted"></i>
                                                <small><?= esc($log['username']) ?></small>
                                            </div>
                                        </div>

                                        <!-- Info Produk -->
                                        <div class="mb-2 pb-1 border-bottom">
                                            <strong><?= esc($log['nama_motif']) ?> - <?= esc($log['nama_warna']) ?></strong>
                                            <div><code><small><?= esc($log['sku']) ?></small></code></div>
                                        </div>

                                        <!-- Stok Info -->
                                        <div class="row g-1 small">
                                            <div class="col-4 text-muted">Stok Sebelum:</div>
                                            <div class="col-8"><?= number_format($log['jumlah_sebelum']) ?></div>

                                            <div class="col-4 text-muted">Perubahan:</div>
                                            <div class="col-8 <?= $perubahanClass ?> fw-bold">
                                                <?= $perubahanSign ?>         <?= number_format($log['jumlah_perubahan']) ?>
                                            </div>

                                            <div class="col-4 text-muted">Stok Sesudah:</div>
                                            <div class="col-8 fw-bold"><?= number_format($log['jumlah_sesudah']) ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-2 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Tidak ada data log stok</p>
                            </div>
                        <?php endif; ?>

                        <div class="mt-3">
                            <?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card .badge {
        font-size: 10px;
    }

    @media (max-width: 768px) {
        .row.g-2>[class*="col-"] {
            padding-left: 4px;
            padding-right: 4px;
        }
    }
</style>

<?= $this->endSection() ?>