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
                    <div class="card mb-3">
                        <div class="card-body p-2 p-md-3">
                            <form method="GET" class="row g-2">
                                <!-- Baris 1: Keyword & Tanggal -->
                                <div class="col-md-4">
                                    <input type="text" name="keyword" class="form-control form-control-sm"
                                        placeholder="Cari SKU / Motif / Warna / Supplier" value="<?= $keyword ?? '' ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="start_date" class="form-control form-control-sm"
                                        value="<?= $start_date ?? '' ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="end_date" class="form-control form-control-sm"
                                        value="<?= $end_date ?? '' ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                                </div>

                                <!-- Baris 2: Dropdown -->
                                <div class="col-md-3">
                                    <select onchange="this.form.submit()" name="filter_supplier" class="form-select form-select-sm">
                                        <option value="">-- Semua Merk/Brand --</option>
                                        <?php foreach ($suppliers as $sup): ?>
                                            <option value="<?= $sup['id'] ?>" <?= ($filter_supplier ?? '') == $sup['id'] ? 'selected' : '' ?>>
                                                <?= esc($sup['nama']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select onchange="this.form.submit()" name="filter_motif" class="form-select form-select-sm">
                                        <option value="">-- Semua Motif --</option>
                                        <?php foreach ($motifs as $mot): ?>
                                            <option value="<?= $mot['id'] ?>" <?= ($filter_motif ?? '') == $mot['id'] ? 'selected' : '' ?>>
                                                <?= esc($mot['nama_motif']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select onchange="this.form.submit()" name="filter_warna" class="form-select form-select-sm">
                                        <option value="">-- Semua Warna --</option>
                                        <?php foreach ($warnas as $wrn): ?>
                                            <option value="<?= $wrn['id'] ?>" <?= ($filter_warna ?? '') == $wrn['id'] ? 'selected' : '' ?>>
                                                <?= esc($wrn['nama_warna']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select onchange="this.form.submit()" name="filter_stok" class="form-select form-select-sm">
                                        <option value="">-- Semua Stok --</option>
                                        <option value="aman" <?= ($filter_stok ?? '') == 'aman' ? 'selected' : '' ?>>Aman
                                            (Stok > Min)</option>
                                        <option value="menipis" <?= ($filter_stok ?? '') == 'menipis' ? 'selected' : '' ?>>
                                            Menipis (Stok ≤ Min, >0)</option>
                                        <option value="habis" <?= ($filter_stok ?? '') == 'habis' ? 'selected' : '' ?>>
                                            Habis (Stok = 0)</option>
                                    </select>
                                </div>

                                <div class="col-12 text-end mt-2">
                                    <a href="<?= base_url('pemilik/laporan/stok') ?>"
                                        class="btn btn-secondary btn-sm">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tombol Export & Info -->
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <a href="<?= base_url('pemilik/laporan/export-stok?' . http_build_query($_GET)) ?>"
                            class="btn btn-success btn-sm">
                            <i class="bi bi-file-excel"></i> Export Excel
                        </a>
                    </div>

                    <!-- Informasi Periode -->
                    <?php if (!empty($start_date) && !empty($end_date)): ?>
                        <div class="alert alert-info py-2">
                            <i class="bi bi-calendar"></i> Menampilkan stok yang terakhir diupdate antara
                            <strong><?= date('d/m/Y', strtotime($start_date)) ?></strong> s.d
                            <strong><?= date('d/m/Y', strtotime($end_date)) ?></strong>
                            <?php if (!empty($keyword)): ?> | Keyword: <strong><?= esc($keyword) ?></strong><?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Ringkasan Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-6 mb-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">Total Produk</h6>
                                    <h4 class="mb-0"><?= number_format($summary['total_produk'] ?? 0) ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="card bg-success text-white">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">Total Stok</h6>
                                    <h4 class="mb-0"><?= number_format($summary['total_stok'] ?? 0) ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="card bg-warning text-dark">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">Stok Menipis</h6>
                                    <h4 class="mb-0"><?= number_format($summary['produk_menipis'] ?? 0) ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">Stok Habis</h6>
                                    <h4 class="mb-0"><?= number_format($summary['produk_habis'] ?? 0) ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========== TABEL (Desktop) ========== -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>SKU</th>
                                        <th>Supplier</th>
                                        <th>Motif</th>
                                        <th>Warna</th>
                                        <th class="text-center">Stok</th>
                                        <th class="text-center">Min</th>
                                        <th class="text-center">Status</th>
                                        <th>Update Terakhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($produk)): ?>
                                        <?php $no = 1;
                                        foreach ($produk as $row): ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><code><?= esc($row['sku']) ?></code></td>
                                                <td><?= esc($row['nama_supplier']) ?></td>
                                                <td><?= esc($row['nama_motif']) ?></td>
                                                <td><?= esc($row['nama_warna']) ?></td>
                                                <td class="text-center">
                                                    <?php if ($row['stok'] == 0): ?>
                                                        <span class="badge bg-danger"><?= number_format($row['stok']) ?></span>
                                                    <?php elseif ($row['stok'] <= $row['min_stok']): ?>
                                                        <span
                                                            class="badge bg-warning text-dark"><?= number_format($row['stok']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success"><?= number_format($row['stok']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= number_format($row['min_stok']) ?></td>
                                                <td class="text-center">
                                                    <?php if ($row['stok'] == 0): ?>
                                                        <span class="badge bg-danger">Habis</span>
                                                    <?php elseif ($row['stok'] <= $row['min_stok']): ?>
                                                        <span class="badge bg-warning text-dark">Menipis</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Aman</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><small><?= date('d/m/Y H:i', strtotime($row['updated_at'])) ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada data stok yang sesuai filter</td>
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
                        <?php if (!empty($produk)): ?>
                            <?php $no = 1;
                            foreach ($produk as $row): ?>
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                if ($row['stok'] == 0) {
                                    $statusClass = 'bg-danger';
                                    $statusText = 'Habis';
                                } elseif ($row['stok'] <= $row['min_stok']) {
                                    $statusClass = 'bg-warning text-dark';
                                    $statusText = 'Menipis';
                                } else {
                                    $statusClass = 'bg-success';
                                    $statusText = 'Aman';
                                }
                                ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body p-2">
                                        <!-- Header Card -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge bg-secondary">#<?= $no++ ?></span>
                                                <code class="ms-1 small"><?= esc($row['sku']) ?></code>
                                            </div>
                                            <span class="badge <?= $statusClass ?> px-2 py-1"><?= $statusText ?></span>
                                        </div>

                                        <!-- Info Produk -->
                                        <div class="row g-1 small">
                                            <div class="col-4 text-muted">Supplier:</div>
                                            <div class="col-8 fw-semibold"><?= esc($row['nama_supplier']) ?></div>

                                            <div class="col-4 text-muted">Motif:</div>
                                            <div class="col-8"><?= esc($row['nama_motif']) ?></div>

                                            <div class="col-4 text-muted">Warna:</div>
                                            <div class="col-8"><?= esc($row['nama_warna']) ?></div>

                                            <div class="col-4 text-muted">Stok:</div>
                                            <div class="col-8">
                                                <?php if ($row['stok'] == 0): ?>
                                                    <span class="text-danger fw-bold"><?= number_format($row['stok']) ?></span>
                                                <?php elseif ($row['stok'] <= $row['min_stok']): ?>
                                                    <span class="text-warning fw-bold"><?= number_format($row['stok']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-success fw-bold"><?= number_format($row['stok']) ?></span>
                                                <?php endif; ?>
                                                <span class="text-muted"> / Min: <?= number_format($row['min_stok']) ?></span>
                                            </div>

                                            <div class="col-4 text-muted">Update:</div>
                                            <div class="col-8">
                                                <small><?= date('d/m/Y H:i', strtotime($row['updated_at'])) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-2 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Tidak ada data stok yang sesuai filter</p>
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