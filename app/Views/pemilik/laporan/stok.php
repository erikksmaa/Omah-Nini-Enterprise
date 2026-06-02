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
                                <div class="col-md-4 col-12">
                                    <label class="form-label small mb-1">Cari Produk</label>
                                    <input type="text" name="keyword" class="form-control form-control-sm"
                                        placeholder="SKU / Motif / Warna / Supplier"
                                        value="<?= $keyword ?? '' ?>">
                                </div>
                                <div class="col-md-4 col-6">
                                    <label class="form-label small mb-1">Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm"
                                        value="<?= $start_date ?? '' ?>">
                                </div>
                                <div class="col-md-4 col-6">
                                    <label class="form-label small mb-1">Tanggal Akhir</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm"
                                        value="<?= $end_date ?? '' ?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1">Merk / Brand</label>
                                    <select name="filter_supplier" class="form-select form-select-sm">
                                        <option value="">-- Semua Merk --</option>
                                        <?php foreach ($suppliers as $sup): ?>
                                            <option value="<?= $sup['id'] ?>" <?= ($filter_supplier ?? '') == $sup['id'] ? 'selected' : '' ?>>
                                                <?= esc($sup['nama']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1">Motif</label>
                                    <select name="filter_motif" class="form-select form-select-sm">
                                        <option value="">-- Semua Motif --</option>
                                        <?php foreach ($motifs as $mot): ?>
                                            <option value="<?= $mot['id'] ?>" <?= ($filter_motif ?? '') == $mot['id'] ? 'selected' : '' ?>>
                                                <?= esc($mot['nama_motif']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1">Warna</label>
                                    <select name="filter_warna" class="form-select form-select-sm">
                                        <option value="">-- Semua Warna --</option>
                                        <?php foreach ($warnas as $wrn): ?>
                                            <option value="<?= $wrn['id'] ?>" <?= ($filter_warna ?? '') == $wrn['id'] ? 'selected' : '' ?>>
                                                <?= esc($wrn['nama_warna']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1">Status Stok</label>
                                    <select name="filter_stok" class="form-select form-select-sm">
                                        <option value="">-- Semua Stok --</option>
                                        <option value="aman"    <?= ($filter_stok ?? '') == 'aman'    ? 'selected' : '' ?>>Aman (Stok > Min)</option>
                                        <option value="menipis" <?= ($filter_stok ?? '') == 'menipis' ? 'selected' : '' ?>>Menipis (Stok ≤ Min, >0)</option>
                                        <option value="habis"   <?= ($filter_stok ?? '') == 'habis'   ? 'selected' : '' ?>>Habis (Stok = 0)</option>
                                    </select>
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <a href="<?= base_url('pemilik/laporan/stok') ?>"
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
                                Update antara <strong><?= date('d/m/Y', strtotime($start_date)) ?></strong>
                                s.d <strong><?= date('d/m/Y', strtotime($end_date)) ?></strong>
                                <?php if (!empty($keyword)): ?>
                                    &mdash; Keyword: <strong><?= esc($keyword) ?></strong>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>
                        <a href="<?= base_url('pemilik/laporan/export-stok?' . http_build_query($_GET)) ?>"
                            class="btn btn-success btn-sm">
                            <i class="bi bi-file-excel"></i> Export Excel
                        </a>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-3 g-2">
                        <div class="col-6 col-md-3">
                            <div class="card bg-primary h-100">
                                <div class="card-body py-1 px-3">
                                    <div class="small text-white mb-1">
                                        <i class="bi bi-grid text-white"></i> Total Produk
                                    </div>
                                    <div class="fw-bold text-white fs-5">
                                        <?= number_format($summary['total_produk'] ?? 0) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-success h-100">
                                <div class="card-body py-1 px-3">
                                    <div class="small text-white mb-1">
                                        <i class="bi bi-boxes text-white"></i> Total Stok
                                    </div>
                                    <div class="fw-bold text-white fs-5">
                                        <?= number_format($summary['total_stok'] ?? 0) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-warning h-100">
                                <div class="card-body py-1 px-3">
                                    <div class="small text-white mb-1">
                                        <i class="bi bi-exclamation-triangle text-white"></i> Stok Menipis
                                    </div>
                                    <div class="fw-bold text-white fs-5">
                                        <?= number_format($summary['produk_menipis'] ?? 0) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-danger h-100">
                                <div class="card-body py-1 px-3">
                                    <div class="small text-white mb-1">
                                        <i class="bi bi-x-circle text-white"></i> Stok Habis
                                    </div>
                                    <div class="fw-bold text-white fs-5">
                                        <?= number_format($summary['produk_habis'] ?? 0) ?>
                                    </div>
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
                                        <th class="text-center" width="4%">No</th>
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
                                        <?php $no = 1; foreach ($produk as $row):
                                            if ($row['stok'] == 0)                    { $sc = 'bg-danger';            $st = 'Habis'; }
                                            elseif ($row['stok'] <= $row['min_stok']) { $sc = 'bg-warning text-dark'; $st = 'Menipis'; }
                                            else                                       { $sc = 'bg-success';           $st = 'Aman'; }
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><code><?= esc($row['sku']) ?></code></td>
                                                <td><?= esc($row['nama_supplier']) ?></td>
                                                <td><?= esc($row['nama_motif']) ?></td>
                                                <td><?= esc($row['nama_warna']) ?></td>
                                                <td class="text-center">
                                                    <span class="badge <?= $sc ?>"><?= number_format($row['stok']) ?></span>
                                                </td>
                                                <td class="text-center text-muted"><?= number_format($row['min_stok']) ?></td>
                                                <td class="text-center">
                                                    <span class="badge <?= $sc ?>"><?= $st ?></span>
                                                </td>
                                                <td>
                                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($row['updated_at'])) ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                                                <span class="text-muted">Tidak ada data stok yang sesuai filter</span>
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
                        <?php if (!empty($produk)): ?>
                            <?php $no = 1; foreach ($produk as $row):
                                if ($row['stok'] == 0)                    { $sc = 'bg-danger';            $st = 'Habis';   $tc = 'text-danger'; }
                                elseif ($row['stok'] <= $row['min_stok']) { $sc = 'bg-warning text-dark'; $st = 'Menipis'; $tc = 'text-warning'; }
                                else                                       { $sc = 'bg-success';           $st = 'Aman';    $tc = 'text-success'; }
                            ?>
                                <div class="card mb-2 border shadow-sm">
                                    <div class="card-body p-2">

                                        <!-- Header -->
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <div>
                                                <span class="badge bg-secondary me-1">#<?= $no++ ?></span>
                                                <code class="small text-primary"><?= esc($row['sku']) ?></code>
                                            </div>
                                            <span class="badge <?= $sc ?>"><?= $st ?></span>
                                        </div>

                                        <!-- Info Produk -->
                                        <div class="row g-1 small mb-2">
                                            <div class="col-4 text-muted">Supplier:</div>
                                            <div class="col-8 fw-semibold"><?= esc($row['nama_supplier']) ?></div>

                                            <div class="col-4 text-muted">Motif:</div>
                                            <div class="col-8"><?= esc($row['nama_motif']) ?></div>

                                            <div class="col-4 text-muted">Warna:</div>
                                            <div class="col-8"><?= esc($row['nama_warna']) ?></div>
                                        </div>

                                        <!-- Info Stok -->
                                        <div class="small text-muted mb-1 pb-1 border-bottom">
                                            <i class="bi bi-bar-chart"></i> Info Stok
                                        </div>
                                        <div class="row g-1 small">
                                            <div class="col-4 text-muted">Stok:</div>
                                            <div class="col-8 fw-bold <?= $tc ?>"><?= number_format($row['stok']) ?></div>

                                            <div class="col-4 text-muted">Min Stok:</div>
                                            <div class="col-8 text-muted"><?= number_format($row['min_stok']) ?></div>

                                            <div class="col-4 text-muted">Update:</div>
                                            <div class="col-8 text-muted">
                                                <small><?= date('d/m/Y H:i', strtotime($row['updated_at'])) ?></small>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                                <span class="text-muted">Tidak ada data stok yang sesuai filter</span>
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