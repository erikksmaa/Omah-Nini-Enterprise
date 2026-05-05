<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary">Total Produk: <?= $total_produk ?? 0 ?></span>
                            <span class="badge bg-success">Total Stok: <?= number_format($total_stok ?? 0) ?> Potong</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-2 p-md-3">

                    <!-- Filter Form -->
                    <div class="card mb-3 shadow-sm bg-light">
                        <div class="card-body p-2 p-md-3">
                            <form method="get" class="row g-2">
                                <div class="col-md-4">
                                    <select name="supplier" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">-- Semua Merk --</option>
                                        <?php foreach ($suppliers as $sup): ?>
                                            <option value="<?= $sup['id'] ?>" <?= ($selectedSupplier ?? '') == $sup['id'] ? 'selected' : '' ?>>
                                                <?= esc($sup['nama']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">-- Semua Status --</option>
                                        <option value="aman" <?= ($selectedStatus ?? '') == 'aman' ? 'selected' : '' ?>>✅ Stok Aman</option>
                                        <option value="menipis" <?= ($selectedStatus ?? '') == 'menipis' ? 'selected' : '' ?>>⚠️ Stok Menipis</option>
                                        <option value="habis" <?= ($selectedStatus ?? '') == 'habis' ? 'selected' : '' ?>>❌ Stok Habis</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <a href="<?= base_url('karyawan/stok') ?>" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-arrow-repeat"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ========== TABEL (Desktop) ========== -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>SKU</th>
                                        <th>Merk</th>
                                        <th>Motif</th>
                                        <th>Warna</th>
                                        <th class="text-center">Stok</th>
                                        <th class="text-center">Min. Stok</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($produk)): ?>
                                    <?php foreach ($produk as $row): ?>
                                        <?php
                                        $statusClass = '';
                                        $statusText  = '';
                                        if ($row['stok'] == 0) {
                                            $statusClass = 'bg-danger';
                                            $statusText  = 'Habis';
                                        } elseif ($row['stok'] <= $row['min_stok']) {
                                            $statusClass = 'bg-warning text-dark';
                                            $statusText  = 'Menipis';
                                        } else {
                                            $statusClass = 'bg-success';
                                            $statusText  = 'Aman';
                                        }
                                        ?>
                                        <tr>
                                            <td><code><?= esc($row['sku']) ?></code></td>
                                            <td><?= esc($row['nama_supplier']) ?></td>
                                            <td><?= esc($row['nama_motif']) ?></td>
                                            <td><?= esc($row['nama_warna']) ?></td>
                                            <td class="text-center fw-bold">
                                                <?php if ($row['stok'] == 0): ?>
                                                    <span class="text-danger"><?= number_format($row['stok']) ?></span>
                                                <?php elseif ($row['stok'] <= $row['min_stok']): ?>
                                                    <span class="text-warning"><?= number_format($row['stok']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-success"><?= number_format($row['stok']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= number_format($row['min_stok']) ?></td>
                                            <td class="text-center">
                                                <span class="badge <?= $statusClass ?> px-2 py-1"><?= $statusText ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('karyawan/stok/detail/' . $row['id']) ?>" class="btn btn-outline-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('karyawan/stok/opname/' . $row['id']) ?>" class="btn btn-outline-warning" title="Opname">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="bi bi-inbox fs-2 text-muted"></i>
                                            <p class="text-muted mt-2 mb-0">Tidak ada data produk</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?>
                    </div>

                    <!-- ========== CARD VIEW (Mobile) ========== -->
                    <div class="d-md-none">
                        <?php if (!empty($produk)): ?>
                            <?php foreach ($produk as $row): ?>
                                <?php
                                $statusClass = '';
                                $statusText  = '';
                                if ($row['stok'] == 0) {
                                    $statusClass = 'bg-danger';
                                    $statusText  = 'Habis';
                                } elseif ($row['stok'] <= $row['min_stok']) {
                                    $statusClass = 'bg-warning text-dark';
                                    $statusText  = 'Menipis';
                                } else {
                                    $statusClass = 'bg-success';
                                    $statusText  = 'Aman';
                                }
                                ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body p-2">
                                        <!-- Header Card -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge bg-secondary me-1"><?= esc($row['sku']) ?></span>
                                                <span class="badge <?= $statusClass ?> px-2 py-1"><?= $statusText ?></span>
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('karyawan/stok/detail/' . $row['id']) ?>" class="btn btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('karyawan/stok/opname/' . $row['id']) ?>" class="btn btn-outline-warning" title="Opname">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Info Produk -->
                                        <div class="row g-1 small">
                                            <div class="col-4 text-muted">Merk:</div>
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
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-2 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Tidak ada data produk</p>
                            </div>
                        <?php endif; ?>

                        <!-- Pagination -->
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
    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
    }
</style>

<?= $this->endSection() ?>