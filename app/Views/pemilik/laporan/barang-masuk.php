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
                                    <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                                        value="<?= $tanggalMulai ?? '' ?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="form-control form-control-sm"
                                        value="<?= $tanggalAkhir ?? '' ?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small mb-1">Merk / Brand</label>
                                    <select name="supplier" class="form-select form-select-sm">
                                        <option value="">-- Semua Merk --</option>
                                        <?php foreach ($suppliers as $sup): ?>
                                            <option value="<?= $sup['id'] ?>" <?= ($selectedSupplier ?? '') == $sup['id'] ? 'selected' : '' ?>>
                                                <?= esc($sup['nama']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6 d-flex gap-2">
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

                    <!-- Toolbar: Export & Info Periode -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <?php if (!empty($tanggalMulai) && !empty($tanggalAkhir)): ?>
                            <div class="alert alert-info py-1 px-2 mb-0 small">
                                <i class="bi bi-calendar"></i>
                                <strong><?= date('d/m/Y', strtotime($tanggalMulai)) ?></strong>
                                s.d
                                <strong><?= date('d/m/Y', strtotime($tanggalAkhir)) ?></strong>
                            </div>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>
                        <a href="<?= base_url('pemilik/laporan/export-barang-masuk?' . http_build_query($_GET)) ?>"
                            class="btn btn-success btn-sm">
                            <i class="bi bi-file-excel"></i> Export Excel
                        </a>
                    </div>

                    <!-- ========== TABEL (Desktop) ========== -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="4%">No</th>
                                        <th>No. Invoice</th>
                                        <th>Merk / Brand</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Jumlah Item</th>
                                        <th>Detail Item</th>
                                        <th width="18%">Catatan</th>
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
                                                <td class="text-center fw-bold"><?= $pemb['total_items'] ?></td>
                                                <td>
                                                    <?php if (!empty($pemb['items'])): ?>
                                                        <ul class="mb-0 ps-3">
                                                            <?php foreach ($pemb['items'] as $item): ?>
                                                                <li><?= esc($item['nama_produk']) ?> <span class="text-muted">(<?= $item['jumlah'] ?> pcs)</span></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-muted small"><?= esc($pemb['catatan'] ?? '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                                                <span class="text-muted">Tidak ada data pembelian</span>
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
                        <?php if (!empty($pembelian)): ?>
                            <?php foreach ($pembelian as $pemb): ?>
                                <div class="card mb-2 border shadow-sm">
                                    <div class="card-body p-2">

                                        <!-- Header -->
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <code class="small text-primary"><?= esc($pemb['no_invoice']) ?></code>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($pemb['tanggal_pembelian'])) ?></small>
                                        </div>

                                        <!-- Info Utama -->
                                        <div class="row g-1 small mb-2">
                                            <div class="col-4 text-muted">Merk:</div>
                                            <div class="col-8 fw-semibold"><?= esc($pemb['nama_supplier']) ?></div>
                                        </div>

                                        <!-- Detail Barang -->
                                        <div class="small text-muted mb-1 pb-1 border-bottom">
                                            <i class="bi bi-box-seam"></i> Detail Barang
                                            <span class="badge bg-secondary ms-1"><?= $pemb['total_items'] ?> item</span>
                                        </div>
                                        <?php if (!empty($pemb['items'])): ?>
                                            <div class="bg-light rounded p-2 small mb-2">
                                                <?php foreach ($pemb['items'] as $item): ?>
                                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                                        <span><?= esc($item['nama_produk']) ?></span>
                                                        <span class="fw-bold text-muted">×<?= $item['jumlah'] ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-muted small mb-2">-</div>
                                        <?php endif; ?>

                                        <!-- Catatan -->
                                        <?php if (!empty($pemb['catatan'])): ?>
                                            <div class="small text-muted pt-1 border-top">
                                                <i class="bi bi-chat-text"></i> <?= esc($pemb['catatan']) ?>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                                <span class="text-muted">Tidak ada data pembelian</span>
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