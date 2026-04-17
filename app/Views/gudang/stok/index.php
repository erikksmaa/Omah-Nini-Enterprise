<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Statistik Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Produk</h6>
                            <h2 class="mb-0"><?= number_format($total_produk) ?></h2>
                        </div>
                        <i class="bi bi-box fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Stok Menipis</h6>
                            <h2 class="mb-0"><?= number_format($stok_menipis) ?></h2>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Stok Habis</h6>
                            <h2 class="mb-0"><?= number_format($stok_habis) ?></h2>
                        </div>
                        <i class="bi bi-x-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Nilai Stok</h6>
                            <h4 class="mb-0">Rp <?= number_format($total_nilai_stok, 0, ',', '.') ?></h4>
                        </div>
                        <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan Tabel -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-box-seam"></i> Manajemen Stok</h5>
        </div>
        <div class="card-body p-3">
            <!-- Filter Form -->
            <form method="GET" class="row mb-3">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Cari produk..." value="<?= $keyword ?>">
                </div>
                <div class="col-md-3">
                    <select name="kategori_id" class="form-control">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori as $kat): ?>
                            <option value="<?= $kat['id'] ?>" <?= $kategori_id == $kat['id'] ? 'selected' : '' ?>>
                                <?= $kat['nama'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status_stok" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="aman" <?= $status_stok == 'aman' ? 'selected' : '' ?>>Stok Aman</option>
                        <option value="menipis" <?= $status_stok == 'menipis' ? 'selected' : '' ?>>Stok Menipis</option>
                        <option value="habis" <?= $status_stok == 'habis' ? 'selected' : '' ?>>Stok Habis</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Min Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produk as $item): ?>
                            <?php 
                            $statusClass = '';
                            $statusText = '';
                            if ($item['stok'] <= 0) {
                                $statusClass = 'bg-danger';
                                $statusText = 'HABIS';
                            } elseif ($item['stok'] <= $item['min_stok']) {
                                $statusClass = 'bg-warning';
                                $statusText = 'MENIPIS';
                            } else {
                                $statusClass = 'bg-success';
                                $statusText = 'AMAN';
                            }
                            ?>
                            <tr>
                                <td><?= $item['sku'] ?></td>
                                <td><strong><?= $item['nama_barang'] ?></strong></td>
                                <td><?= $item['nama_kategori'] ?? '-' ?></td>
                                <td>Rp <?= number_format($item['harga_beli'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?></td>
                                <td class="fw-bold <?= $item['stok'] <= $item['min_stok'] ? 'text-danger' : '' ?>">
                                    <?= number_format($item['stok']) ?>
                                </td>
                                <td><?= number_format($item['min_stok']) ?></td>
                                <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                                <td>
                                    <a href="<?= base_url('gudang/stok/detail/' . $item['id']) ?>" class="btn btn-sm btn-secondary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <a href="<?= base_url('gudang/stok/opname/' . $item['id']) ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Opname
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($produk)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data produk</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                <?= $pager->links('default', 'bootstrap_pagination') ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Nonaktifkan DataTable karena pakai CI4 pagination
        // DataTable hanya untuk styling
    });
</script>
<?= $this->endSection() ?>