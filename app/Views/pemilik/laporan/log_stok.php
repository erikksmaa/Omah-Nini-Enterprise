<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-2 px-md-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
                </div>
                <div class="card-body">

                    <!-- Filter Form -->
                    <div class="card mb-3 bg-light">
                        <div class="card-body py-2">
                            <form method="GET" class="row g-2">
                                <div class="col-md-3 col-6">
                                    <input type="date" name="start_date" class="form-control form-control-sm"
                                        value="<?= $start_date ?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input type="date" name="end_date" class="form-control form-control-sm"
                                        value="<?= $end_date ?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <select name="id_produk" class="form-select form-select-sm">
                                        <option value="">Semua Produk</option>
                                        <?php foreach ($produk_list as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= ($filter_produk ?? '') == $p['id'] ? 'selected' : '' ?>>
                                                <?= esc($p['nama_motif'] ?? $p['nama_produk']) ?> -
                                                <?= esc($p['nama_warna'] ?? '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <select name="tipe_ref" class="form-select form-select-sm">
                                        <option value="">Semua Tipe</option>
                                        <option value="pembelian" <?= ($filter_tipe ?? '') == 'pembelian' ? 'selected' : '' ?>>Pembelian (Stok +)</option>
                                        <option value="penjualan" <?= ($filter_tipe ?? '') == 'penjualan' ? 'selected' : '' ?>>Penjualan (Stok -)</option>
                                        <option value="penyesuaian" <?= ($filter_tipe ?? '') == 'penyesuaian' ? 'selected' : '' ?>>Penyesuaian (Opname)</option>
                                    </select>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                    <a href="<?= base_url('admin/laporan/log-stok') ?>"
                                        class="btn btn-secondary btn-sm">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <a href="<?= base_url('admin/laporan/export-log-stok?' . http_build_query($_GET)) ?>"
                            class="btn btn-success btn-sm">
                            <i class="bi bi-file-excel"></i> Export Excel
                        </a>
                        <!-- form filter yang sudah ada -->
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-3">
                        <div class="col-md-4 col-6 mb-2">
                            <div class="card bg-success text-white">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">Pembelian (Stok +)</h6>
                                    <?php $total_beli = 0;
                                    foreach ($summary as $s):
                                        if ($s['tipe_ref'] == 'pembelian')
                                            $total_beli = $s['total_perubahan']; endforeach; ?>
                                    <h5 class="mb-0">+ <?= number_format($total_beli) ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-6 mb-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">Penjualan (Stok -)</h6>
                                    <?php $total_jual = 0;
                                    foreach ($summary as $s):
                                        if ($s['tipe_ref'] == 'penjualan')
                                            $total_jual = abs($s['total_perubahan']); endforeach; ?>
                                    <h5 class="mb-0">- <?= number_format($total_jual) ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-6 mb-2">
                            <div class="card bg-info text-white">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">Penyesuaian</h6>
                                    <?php $total_adj = 0;
                                    foreach ($summary as $s):
                                        if ($s['tipe_ref'] == 'penyesuaian')
                                            $total_adj = $s['total_perubahan']; endforeach; ?>
                                    <h5 class="mb-0"><?= number_format($total_adj) ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Log Stok -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>User</th>
                                    <th>Produk</th>
                                    <th>Tipe</th>
                                    <th class="text-center">Stok Sebelum</th>
                                    <th class="text-center">Perubahan</th>
                                    <th class="text-center">Stok Sesudah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logs)): ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                                            <td><?= esc($log['username']) ?></td>
                                            <td><?= esc($log['nama_motif']) ?> - <?= esc($log['nama_warna']) ?></td>
                                            <td>
                                                <?php
                                                $badge = '';
                                                if ($log['tipe_ref'] == 'pembelian')
                                                    $badge = 'bg-success';
                                                elseif ($log['tipe_ref'] == 'penjualan')
                                                    $badge = 'bg-danger';
                                                else
                                                    $badge = 'bg-info';
                                                ?>
                                                <span class="badge <?= $badge ?>"><?= $log['tipe_ref'] ?></span>
                                            </td>
                                            <td class="text-center"><?= number_format($log['jumlah_sebelum']) ?></td>
                                            <td
                                                class="text-center <?= $log['jumlah_perubahan'] > 0 ? 'text-success' : 'text-danger' ?>">
                                                <?= $log['jumlah_perubahan'] > 0 ? '+' : '' ?>        <?= number_format($log['jumlah_perubahan']) ?>
                                            </td>
                                            <td class="text-center"><?= number_format($log['jumlah_sesudah']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data log stok</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>