<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
                    <a href="<?= base_url('admin/produk/create') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Tambah Produk
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control"
                                placeholder="Cari produk (SKU, motif, warna, supplier)..."
                                value="<?= $keyword ?? '' ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <?php if (!empty($keyword)): ?>
                                <a href="<?= base_url('admin/produk') ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>SKU</th>
                                    <th>Supplier / Brand</th>
                                    <th>Motif</th>
                                    <th>Warna</th>
                                    <th>Stok</th>
                                    <th>Min Stok</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($produk)): ?>
                                    <?php $no = 1 + (($pager->getCurrentPage() - 1) * 10); ?>
                                    <?php foreach ($produk as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><code><?= esc($row['sku']) ?></code></td>
                                            <td><?= esc($row['nama_supplier']) ?></td>
                                            <td><?= esc($row['nama_motif']) ?></td>
                                            <td><?= esc($row['nama_warna']) ?></td>
                                            <td>
                                                <?php if ($row['stok'] <= $row['min_stok'] && $row['stok'] > 0): ?>
                                                    <span
                                                        class="badge bg-warning text-dark"><?= number_format($row['stok']) ?></span>
                                                <?php elseif ($row['stok'] == 0): ?>
                                                    <span class="badge bg-danger"><?= number_format($row['stok']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><?= number_format($row['stok']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= number_format($row['min_stok']) ?></td>
                                            <td class="text-center">
                                                <a href="<?= base_url('admin/produk/edit/' . $row['id']) ?>"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $row['id'] ?>"
                                                    data-sku="<?= esc($row['sku']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data produk</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Delete button handler
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const sku = this.dataset.sku;

            Swal.fire({
                title: 'Yakin hapus?',
                text: `Produk dengan SKU "${sku}" akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?= base_url('admin/produk/delete') ?>/${id}`;
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>