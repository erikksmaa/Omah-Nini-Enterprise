<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Penjualan</h6>
                    <a href="<?= base_url('karyawan/penjualan/create') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-cart-plus"></i> Barang Keluar (POS)
                    </a>
                </div>
                <div class="card-body p-2 p-md-3">

                    <!-- Filter Form -->
                    <div class="card mb-3 bg-light">
                        <div class="card-body p-2 p-md-3">
                            <form method="get" class="row g-2">
                                <div class="col-md-4 col-6">
                                    <label class="form-label small mb-0">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" class="form-control form-control-sm" 
                                           value="<?= $tanggalMulai ?? '' ?>">
                                </div>
                                <div class="col-md-4 col-6">
                                    <label class="form-label small mb-0">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="form-control form-control-sm" 
                                           value="<?= $tanggalAkhir ?? '' ?>">
                                </div>
                                <div class="col-md-4 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <a href="<?= base_url('karyawan/penjualan') ?>" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-arrow-repeat"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
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
                                        <th>Pembeli</th>
                                        <th>Tanggal</th>
                                        <th>Catatan</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($transaksi)): ?>
                                    <?php $no = 1 + (($pager->getCurrentPage() - 1) * ($pager->getPerPage() ?? 10)); ?>
                                    <?php foreach ($transaksi as $row): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><code><?= esc($row['no_invoice']) ?></code></td>
                                            <td>
                                                <?= esc($row['nama_pembeli']) ?>
                                                <?= $row['nama_pelanggan'] ? '<small class="text-muted">(' . esc($row['nama_pelanggan']) . ')</small>' : '' ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_transaksi'])) ?></td>
                                            <td><small><?= esc($row['catatan'] ?? '-') ?></small></td>
                                            <td class="text-center">
                                                <a href="<?= base_url('karyawan/penjualan/struk/' . $row['id']) ?>" class="btn btn-sm btn-info" title="Struk">
                                                    <i class="bi bi-receipt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-inbox fs-2 text-muted"></i>
                                            <p class="text-muted mt-2 mb-0">Belum ada data penjualan</p>
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
                        <?php if (!empty($transaksi)): ?>
                            <?php foreach ($transaksi as $row): ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-body p-2">
                                        <!-- Header Card -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge bg-primary"><?= esc($row['no_invoice']) ?></span>
                                                <small class="text-muted ms-2"><?= date('d/m/Y H:i', strtotime($row['tanggal_transaksi'])) ?></small>
                                            </div>
                                            <a href="<?= base_url('karyawan/penjualan/struk/' . $row['id']) ?>" class="btn btn-sm btn-outline-info" title="Struk">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                        </div>

                                        <!-- Info Pembeli -->
                                        <div class="mb-2">
                                            <div class="row g-1 small">
                                                <div class="col-4 text-muted">Pembeli:</div>
                                                <div class="col-8 fw-semibold"><?= esc($row['nama_pembeli']) ?></div>
                                            </div>
                                            <?php if (!empty($row['nama_pelanggan'])): ?>
                                                <div class="row g-1 small mt-1">
                                                    <div class="col-4 text-muted">Pelanggan:</div>
                                                    <div class="col-8"><?= esc($row['nama_pelanggan']) ?></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Catatan -->
                                        <?php if (!empty($row['catatan'])): ?>
                                            <div class="mt-2 pt-1 border-top">
                                                <small class="text-muted">
                                                    <i class="bi bi-chat-text"></i> <?= esc($row['catatan']) ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-2 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada data penjualan</p>
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
        .row.g-2 > [class*="col-"] {
            padding-left: 4px;
            padding-right: 4px;
        }
    }
</style>

<?= $this->endSection() ?>