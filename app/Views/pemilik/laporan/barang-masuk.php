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
                            <form method="get" class="row g-2">
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-0">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                                        value="<?= $tanggalMulai ?? '' ?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-0">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="form-control form-control-sm"
                                        value="<?= $tanggalAkhir ?? '' ?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-0">Merk / Brand</label>
                                    <select onchange="this.form.submit()" name="supplier" class="form-select form-select-sm">
                                        <option value="">-- Semua Merk --</option>
                                        <?php foreach ($suppliers as $sup): ?>
                                            <option value="<?= $sup['id'] ?>" <?= ($selectedSupplier ?? '') == $sup['id'] ? 'selected' : '' ?>>
                                                <?= esc($sup['nama']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <a href="<?= base_url('pemilik/laporan/barang-masuk') ?>"
                                        class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-arrow-repeat"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tombol Export -->
                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= base_url('pemilik/laporan/export-barang-masuk?' . http_build_query($_GET)) ?>"
                            class="btn btn-success btn-sm">
                            <i class="bi bi-file-excel"></i> Export Excel
                        </a>
                    </div>

                    <!-- Informasi Periode -->
                    <?php if (!empty($tanggalMulai) && !empty($tanggalAkhir)): ?>
                        <div class="alert alert-info py-2">
                            <i class="bi bi-calendar"></i> Menampilkan data dari
                            <strong><?= date('d/m/Y', strtotime($tanggalMulai)) ?></strong> s.d
                            <strong><?= date('d/m/Y', strtotime($tanggalAkhir)) ?></strong>
                        </div>
                    <?php endif; ?>

                    <!-- ========== TABEL (Desktop) ========== -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>No. Invoice</th>
                                        <th>Merk / Brand</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Jumlah Item</th>
                                        <th>Detail Item</th>
                                        <th width="20%">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($pembelian)): ?>
                                        <?php $no = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage()); ?>
                                        <?php foreach ($pembelian as $pemb): ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><code><?= esc($pemb['no_invoice']) ?></code></td>
                                                <td><?= esc($pemb['nama_supplier']) ?></td>
                                                <td><?= date('d/m/Y', strtotime($pemb['tanggal_pembelian'])) ?></td>
                                                <td class="text-center fw-bold"> <?= $pemb['total_items'] ?> </td>
                                                <td>
                                                    <?php if (!empty($pemb['items'])): ?>
                                                        <ul class="mb-0">
                                                            <?php foreach ($pemb['items'] as $item): ?>
                                                                <li><?= esc($item['nama_produk']) ?> (<?= $item['jumlah'] ?> pcs)</li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc($pemb['catatan'] ?? '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="bi bi-inbox fs-2 text-muted"></i>
                                                <p class="text-muted mt-2 mb-0">Tidak ada data pembelian</p>
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
                        <?php if (!empty($pembelian)): ?>
                            <?php foreach ($pembelian as $pemb): ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body p-2">
                                        <!-- Header Card -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge bg-primary"><?= esc($pemb['no_invoice']) ?></span>
                                            </div>
                                            <div>
                                                <small
                                                    class="text-muted"><?= date('d/m/Y', strtotime($pemb['tanggal_pembelian'])) ?></small>
                                            </div>
                                        </div>

                                        <!-- Info Supplier -->
                                        <div class="mb-2">
                                            <i class="bi bi-building text-muted me-1"></i>
                                            <strong><?= esc($pemb['nama_supplier']) ?></strong>
                                        </div>

                                        <!-- Detail Items -->
                                        <div class="mb-2">
                                            <div class="text-muted small mb-1">
                                                <i class="bi bi-box-seam">Detail Barang (<?= $pemb['total_items'] ?> item)</i>
                                                <?php if (!empty($pemb['items'])): ?>
                                                <div class="bg-light p-2 rounded small">
                                                    <?php foreach ($pemb['items'] as $item): ?>
                                                        <div class="d-flex justify-content-between border-bottom py-1">
                                                            <span><?= esc($item['nama_produk']) ?></span>
                                                            <span class="fw-bold">x<?= $item['jumlah'] ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-muted small">-</div>
                                            <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Catatan -->
                                        <?php if (!empty($pemb['catatan'])): ?>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-chat-text"></i> Catatan: <?= esc($pemb['catatan']) ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-2 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Tidak ada data pembelian</p>
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

    @media (max-width: 768px) {
        .row.g-2>[class*="col-"] {
            padding-left: 4px;
            padding-right: 4px;
        }
    }
</style>

<?= $this->endSection() ?>