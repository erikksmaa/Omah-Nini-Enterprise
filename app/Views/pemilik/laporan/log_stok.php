<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
                </div>
                <div class="card-body p-2 p-md-3">

                    <!-- Filter Form -->
                    <div class="card border mb-3">
                        <div class="card-body p-2 p-md-3">
                            <form method="GET" class="row g-2 align-items-end">
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1">Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm"
                                        value="<?= $start_date ?? '' ?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1">Tanggal Akhir</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm"
                                        value="<?= $end_date ?? '' ?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1">Produk</label>
                                    <select name="id_produk" class="form-select form-select-sm">
                                        <option value="">-- Semua Produk --</option>
                                        <?php foreach ($produk_list as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= ($filter_produk ?? '') == $p['id'] ? 'selected' : '' ?>>
                                                <?= esc($p['nama_motif'] ?? $p['nama_produk']) ?> - <?= esc($p['nama_warna'] ?? '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1">Tipe Transaksi</label>
                                    <select name="tipe_ref" class="form-select form-select-sm">
                                        <option value="">-- Semua Tipe --</option>
                                        <option value="pembelian"   <?= ($filter_tipe ?? '') == 'pembelian'   ? 'selected' : '' ?>>Pembelian (Stok +)</option>
                                        <option value="penjualan"   <?= ($filter_tipe ?? '') == 'penjualan'   ? 'selected' : '' ?>>Penjualan (Stok -)</option>
                                        <option value="penyesuaian" <?= ($filter_tipe ?? '') == 'penyesuaian' ? 'selected' : '' ?>>Penyesuaian (Opname)</option>
                                    </select>
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <a href="<?= base_url('pemilik/laporan/log-stok') ?>"
                                        class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-arrow-repeat"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Toolbar: Export & Info Periode -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <?php if (!empty($start_date) && !empty($end_date)): ?>
                            <div class="alert alert-info py-1 px-2 mb-0 small">
                                <i class="bi bi-calendar"></i>
                                <strong><?= date('d/m/Y', strtotime($start_date)) ?></strong>
                                s.d
                                <strong><?= date('d/m/Y', strtotime($end_date)) ?></strong>
                            </div>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>
                        <a href="<?= base_url('pemilik/laporan/export-log-stok?' . http_build_query($_GET)) ?>"
                            class="btn btn-success btn-sm">
                            <i class="bi bi-file-excel"></i> Export Excel
                        </a>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-3 g-2">
                        <div class="col-6">
                            <div class="card bg-success h-100">
                                <div class="card-body py-1 px-3">
                                    <div class="small text-white mb-1">
                                        <i class="bi bi-arrow-down-circle text-white"></i> Pembelian (Stok +)
                                    </div>
                                    <?php
                                        $total_beli = 0;
                                        foreach ($summary as $s):
                                            if ($s['tipe_ref'] == 'pembelian') $total_beli = $s['total_perubahan'];
                                        endforeach;
                                    ?>
                                    <div class="fw-bold text-white fs-5">+ <?= number_format($total_beli) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-danger h-100">
                                <div class="card-body py-1 px-3">
                                    <div class="small text-white mb-1">
                                        <i class="bi bi-arrow-up-circle text-white"></i> Penjualan (Stok -)
                                    </div>
                                    <?php
                                        $total_jual = 0;
                                        foreach ($summary as $s):
                                            if ($s['tipe_ref'] == 'penjualan') $total_jual = abs($s['total_perubahan']);
                                        endforeach;
                                    ?>
                                    <div class="fw-bold text-white fs-5">- <?= number_format($total_jual) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========== TABEL (Desktop) ========== -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>User</th>
                                        <th>Produk</th>
                                        <th class="text-center">Tipe</th>
                                        <th class="text-center">Stok Sebelum</th>
                                        <th class="text-center">Perubahan</th>
                                        <th class="text-center">Stok Sesudah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($logs)): ?>
                                        <?php foreach ($logs as $log):
                                            if ($log['tipe_ref'] == 'pembelian')       $badgeClass = 'bg-success';
                                            elseif ($log['tipe_ref'] == 'penjualan')   $badgeClass = 'bg-danger';
                                            else                                        $badgeClass = 'bg-info text-dark';
                                        ?>
                                            <tr>
                                                <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                                                <td><?= esc($log['username']) ?></td>
                                                <td><?= esc($log['nama_motif']) ?> - <?= esc($log['nama_warna']) ?></td>
                                                <td class="text-center">
                                                    <span class="badge <?= $badgeClass ?>"><?= ucfirst($log['tipe_ref']) ?></span>
                                                </td>
                                                <td class="text-center"><?= number_format($log['jumlah_sebelum']) ?></td>
                                                <td class="text-center fw-bold <?= $log['tipe_ref'] == 'penjualan' ? 'text-danger' : 'text-success' ?>">
                                                    <?php if ($log['tipe_ref'] == 'pembelian'): ?>
                                                        + <?= number_format($log['jumlah_perubahan']) ?>
                                                    <?php elseif ($log['tipe_ref'] == 'penjualan'): ?>
                                                        - <?= number_format(abs($log['jumlah_perubahan'])) ?>
                                                    <?php else: ?>
                                                        <?= $log['jumlah_perubahan'] > 0 ? '+' : '' ?><?= number_format($log['jumlah_perubahan']) ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= number_format($log['jumlah_sesudah']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                                                <span class="text-muted">Tidak ada data log stok</span>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?>
                        </div>
                    </div>

                    <!-- ========== CARD VIEW (Mobile) ========== -->
                    <div class="d-md-none">
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log):
                                if ($log['tipe_ref'] == 'pembelian')       { $badgeClass = 'bg-success'; $perubahanClass = 'text-success'; }
                                elseif ($log['tipe_ref'] == 'penjualan')   { $badgeClass = 'bg-danger';  $perubahanClass = 'text-danger'; }
                                else                                        { $badgeClass = 'bg-info text-dark'; $perubahanClass = 'text-info'; }
                            ?>
                                <div class="card mb-2 border shadow-sm">
                                    <div class="card-body p-2">

                                        <!-- Header -->
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></small>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($log['tipe_ref']) ?></span>
                                        </div>

                                        <!-- Info Produk -->
                                        <div class="row g-1 small mb-2">
                                            <div class="col-4 text-muted">Produk:</div>
                                            <div class="col-8 fw-semibold"><?= esc($log['nama_motif']) ?> - <?= esc($log['nama_warna']) ?></div>

                                            <div class="col-4 text-muted">SKU:</div>
                                            <div class="col-8"><code class="small"><?= esc($log['sku']) ?></code></div>

                                            <div class="col-4 text-muted">User:</div>
                                            <div class="col-8">
                                                <i class="bi bi-person-circle text-muted"></i> <?= esc($log['username']) ?>
                                            </div>
                                        </div>

                                        <!-- Info Stok -->
                                        <div class="small text-muted mb-1 pb-1 border-bottom">
                                            <i class="bi bi-bar-chart"></i> Perubahan Stok
                                        </div>
                                        <div class="row g-1 small">
                                            <div class="col-5 text-muted">Stok Sebelum:</div>
                                            <div class="col-7"><?= number_format($log['jumlah_sebelum']) ?></div>

                                            <div class="col-5 text-muted">Perubahan:</div>
                                            <div class="col-7 fw-bold <?= $perubahanClass ?>">
                                                <?php if ($log['tipe_ref'] == 'pembelian'): ?>
                                                    + <?= number_format($log['jumlah_perubahan']) ?>
                                                <?php elseif ($log['tipe_ref'] == 'penjualan'): ?>
                                                    - <?= number_format(abs($log['jumlah_perubahan'])) ?>
                                                <?php else: ?>
                                                    <?= $log['jumlah_perubahan'] > 0 ? '+' : '' ?><?= number_format($log['jumlah_perubahan']) ?>
                                                <?php endif; ?>
                                            </div>

                                            <div class="col-5 text-muted">Stok Sesudah:</div>
                                            <div class="col-7 fw-bold"><?= number_format($log['jumlah_sesudah']) ?></div>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                                <span class="text-muted">Tidak ada data log stok</span>
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

<?= $this->endSection() ?>