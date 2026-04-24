<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-journal-text"></i> Audit Log Stok</h5>
        </div>
        <div class="card-body p-3">
            <!-- Filter Form -->
            <form method="GET" class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control"
                        value="<?= $start_date ?? date('Y-m-01') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?? date('Y-m-d') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Produk</label>
                    <select name="produk_id" class="form-select">
                        <option value="">Semua Produk</option>
                        <?php foreach ($produk_list as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($produk_id ?? '') == $p['id'] ? 'selected' : '' ?>>
                                <?= esc($p['nama_barang']) ?> (<?= esc($p['sku']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipe</label>
                    <select name="tipe" class="form-select">
                        <option value="">Semua</option>
                        <option value="pembelian" <?= ($tipe ?? '') == 'pembelian' ? 'selected' : '' ?>>Pembelian (Masuk)</option>
                        <option value="penjualan" <?= ($tipe ?? '') == 'penjualan' ? 'selected' : '' ?>>Penjualan (Keluar)</option>
                        <option value="penyesuaian" <?= ($tipe ?? '') == 'penyesuaian' ? 'selected' : '' ?>>Penyesuaian</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- Statistik -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-white bg-success">
                        <div class="card-body p-3">
                            <h6 class="card-title">Total Stok Masuk</h6>
                            <h3 class="mb-0"><?= number_format($total_masuk ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white bg-danger">
                        <div class="card-body p-3">
                            <h6 class="card-title">Total Stok Keluar</h6>
                            <h3 class="mb-0"><?= number_format($total_keluar ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Log -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Histori Perubahan Stok</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tableLog">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>SKU</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Sebelum</th>
                                    <th class="text-end">Perubahan</th>
                                    <th class="text-end">Sesudah</th>
                                    <th>Aktivitas</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($log)): ?>
                                    <?php foreach ($log as $item): ?>
                                        <tr>
                                            <td><?= date('d-m-Y H:i:s', strtotime($item['created_at'])) ?></td>
                                            <td><strong><?= esc($item['nama_barang']) ?></strong></td>
                                            <td><?= esc($item['sku']) ?></td>
                                            <td>
                                                <?php
                                                $badge = $item['tipe_ref'] == 'pembelian' ? 'success' : ($item['tipe_ref'] == 'penjualan' ? 'danger' : 'warning');
                                                $label = $item['tipe_ref'] == 'pembelian' ? 'Masuk' : ($item['tipe_ref'] == 'penjualan' ? 'Keluar' : 'Penyesuaian');
                                                ?>
                                                <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                                            </td>
                                            <td class="text-end"><?= number_format($item['jumlah_sebelum']) ?></td>
                                            <td class="text-end <?= ($item['jumlah_perubahan'] ?? 0) > 0 ? 'text-success' : 'text-danger' ?>">
                                                <?= ($item['jumlah_perubahan'] ?? 0) > 0 ? '+' : '' ?><?= number_format($item['jumlah_perubahan'] ?? 0) ?>
                                            </td>
                                            <td class="text-end"><?= number_format($item['jumlah_sesudah']) ?></td>
                                            <td><?= esc($item['aktivitas']) ?></td>
                                            <td><?= esc($item['username'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada data log stok</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination - PERBAIKAN -->
                    <?php if (isset($pager) && $pager && method_exists($pager, 'links')): ?>
                        <div class="mt-4 d-flex justify-content-center">
                            <?= $pager->links('default', 'bootstrap_pagination') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Matikan DataTable pagination karena pakai CI4 pagination
        $('#tableLog').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[0, 'desc']],
            pageLength: 25,
            paging: false,      // Matikan pagination DataTable
            searching: false,   // Matikan search DataTable
            ordering: false,    // Matikan ordering DataTable
            info: false         // Matikan info DataTable
        });
    });
</script>

<style>
    .pagination {
        justify-content: center;
    }
    .page-link {
        color: #4f46e5;
    }
    .page-item.active .page-link {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
</style>
<?= $this->endSection() ?>