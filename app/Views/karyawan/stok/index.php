<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4><?= $title ?></h4>
        <div>
            <span class="badge bg-primary me-2">Total Produk: <?= $total_produk ?></span>
            <span class="badge bg-success">Total Stok: <?= number_format($total_stok) ?> Potong</span>
        </div>
    </div>
    <div class="card-body">

        <!-- Filter -->
        <form method="get" class="row mb-4">
            <div class="col-md-4">
                <label class="form-label">Supplier</label>
                <select name="supplier" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Supplier --</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>" <?= $selectedSupplier == $sup['id'] ? 'selected' : '' ?>>
                            <?= esc($sup['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status Stok</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="aman" <?= $selectedStatus == 'aman' ? 'selected' : '' ?>>Stok Aman</option>
                    <option value="menipis" <?= $selectedStatus == 'menipis' ? 'selected' : '' ?>>Stok Menipis</option>
                    <option value="habis" <?= $selectedStatus == 'habis' ? 'selected' : '' ?>>Stok Habis</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <a href="<?= base_url('karyawan/stok') ?>" class="btn btn-outline-secondary">Reset Filter</a>
            </div>
        </form>

        <!-- Tabel Produk -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>SKU</th>
                        <th>Supplier</th>
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
                            <td><?= esc($row['sku']) ?></td>
                            <td><?= esc($row['nama_supplier']) ?></td>
                            <td><?= esc($row['nama_motif']) ?></td>
                            <td><?= esc($row['nama_warna']) ?></td>
                            <td class="text-center fw-bold"><?= $row['stok'] ?></td>
                            <td class="text-center"><?= $row['min_stok'] ?></td>
                            <td class="text-center">
                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('karyawan/stok/detail/' . $row['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= base_url('karyawan/stok/opname/' . $row['id']) ?>" class="btn btn-sm btn-warning" title="Opname">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data produk.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?>
    </div>
</div>

<?= $this->endSection() ?>