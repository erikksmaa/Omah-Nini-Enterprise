<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
    $foto      = $produk['foto'] ?? null;
    $fotoPath  = 'uploads/produk/' . $foto;
    $hasFoto   = !empty($foto) && file_exists(FCPATH . $fotoPath);
    $stok      = (int) $produk['stok'];
    $min       = (int) $produk['min_stok'];
?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Detail Stok Produk</h4>
        <div class="d-flex gap-2">
            <a href="<?= base_url('karyawan/stok/opname/' . $produk['id']) ?>" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil-square"></i> Opname
            </a>
            <a href="<?= base_url('karyawan/stok') ?>" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="row g-4">

            <!-- ── KOLOM KIRI: Gambar ── -->
            <div class="col-md-4 d-flex flex-column align-items-center">
                <?php if ($hasFoto): ?>
                    <img
                        src="<?= base_url($fotoPath) ?>"
                        alt="Foto <?= esc($produk['nama_warna']) ?>"
                        class="img-thumbnail rounded shadow-sm w-100"
                        style="max-height:300px; object-fit:cover; cursor:zoom-in;"
                        data-bs-toggle="modal"
                        data-bs-target="#modalFoto"
                    >
                    <small class="text-muted mt-2">
                        <i class="bi bi-zoom-in"></i> Klik untuk perbesar
                    </small>
                <?php else: ?>
                    <div class="border rounded d-flex flex-column align-items-center
                                justify-content-center bg-light text-muted w-100"
                         style="min-height:220px;">
                        <i class="bi bi-image" style="font-size:3rem;"></i>
                        <small class="mt-2">Tidak ada foto</small>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ── KOLOM KANAN: Status + Info ── -->
            <div class="col-md-8">

                <!-- Status Stok (paling atas) -->
                <?php if ($stok == 0): ?>
                    <div class="alert alert-danger py-2 mb-3">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong>Stok Habis!</strong> Segera lakukan pembelian.
                    </div>
                <?php elseif ($stok <= $min): ?>
                    <div class="alert alert-warning py-2 mb-3">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong>Stok Menipis!</strong>
                        Tersedia <strong><?= $stok ?></strong> dari minimal <strong><?= $min ?></strong> potong.
                    </div>
                <?php else: ?>
                    <div class="alert alert-success py-2 mb-3">
                        <i class="bi bi-check-circle-fill"></i>
                        <strong>Stok Aman.</strong>
                    </div>
                <?php endif; ?>

                <!-- Tabel Info Produk -->
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width:140px;" class="text-muted fw-normal">SKU</th>
                        <td>
                            <code><?= esc($produk['sku']) ?></code>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Merek</th>
                        <td><?= esc($produk['nama_supplier'] ?? $produk['nama'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Motif</th>
                        <td><?= esc($produk['nama_motif']) ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Warna</th>
                        <td><?= esc($produk['nama_warna']) ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Stok Saat Ini</th>
                        <td>
                            <strong class="<?= $stok == 0 ? 'text-danger' : ($stok <= $min ? 'text-warning' : 'text-success') ?>">
                                <?= $stok ?>
                            </strong> potong
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Stok Minimal</th>
                        <td><?= $min ?> potong</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Keterangan</th>
                        <td><?= esc($produk['keterangan'] ?? '—') ?></td>
                    </tr>
                </table>
            </div>

        </div><!-- /row -->
    </div><!-- /card-body -->
</div>

<!-- ── Modal Zoom Foto ── -->
<?php if ($hasFoto): ?>
<div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0">
                    <?= esc($produk['nama_motif']) ?> —
                    <?= esc($produk['nama_warna']) ?>
                    <small class="text-muted ms-1"><?= esc($produk['sku']) ?></small>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img src="<?= base_url($fotoPath) ?>"
                     alt="Foto produk"
                     class="img-fluid rounded-bottom"
                     style="max-height:80vh; object-fit:contain;">
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ── Riwayat Perubahan Stok ── -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Riwayat Perubahan Stok</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Tgl / Waktu</th>
                        <th>Tipe</th>
                        <th class="text-end">Sebelum</th>
                        <th class="text-end">Perubahan</th>
                        <th class="text-end">Sesudah</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($log_stok)): ?>
                    <?php foreach ($log_stok as $log): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                            <td>
                                <?php
                                $badge = match($log['tipe_ref']) {
                                    'pembelian'   => 'success',
                                    'penjualan'   => 'danger',
                                    'penyesuaian' => 'warning',
                                    default       => 'secondary',
                                };
                                ?>
                                <span class="badge bg-<?= $badge ?>">
                                    <?= ucfirst($log['tipe_ref']) ?>
                                </span>
                            </td>
                            <td class="text-end"><?= $log['jumlah_sebelum'] ?></td>
                            <td class="text-end">
                                <span class="fw-semibold <?= $log['jumlah_perubahan'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $log['jumlah_perubahan'] >= 0
                                        ? '+' . $log['jumlah_perubahan']
                                        : $log['jumlah_perubahan'] ?>
                                </span>
                            </td>
                            <td class="text-end"><?= $log['jumlah_sesudah'] ?></td>
                            <td><?= esc($log['username']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            Belum ada riwayat perubahan stok.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= $pager ? $pager->links('log', 'bootstrap_pagination') : '' ?>
    </div>
</div>

<?= $this->endSection() ?>