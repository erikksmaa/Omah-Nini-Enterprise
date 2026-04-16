<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Histori Mutasi Stok</h5>
        </div>
        <div class="card-body p-3">
            <!-- Filter Form -->
            <form method="GET" class="row mb-3">
                <div class="col-md-3">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                <div class="col-md-3">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
                <div class="col-md-3">
                    <label>Produk</label>
                    <select name="produk_id" class="form-control">
                        <option value="">Semua Produk</option>
                        <?php foreach ($produk_list as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $produk_id == $p['id'] ? 'selected' : '' ?>>
                                <?= $p['nama_barang'] ?> (<?= $p['sku'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Tipe</label>
                    <select name="tipe" class="form-control">
                        <option value="">Semua</option>
                        <option value="pembelian" <?= $tipe == 'pembelian' ? 'selected' : '' ?>>Pembelian (Masuk)</option>
                        <option value="penjualan" <?= $tipe == 'penjualan' ? 'selected' : '' ?>>Penjualan (Keluar)</option>
                        <option value="penyesuaian" <?= $tipe == 'penyesuaian' ? 'selected' : '' ?>>Penyesuaian</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            <!-- Statistik -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <strong>Total Stok Masuk:</strong> <?= number_format($total_masuk) ?> unit
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-danger">
                        <strong>Total Stok Keluar:</strong> <?= number_format($total_keluar) ?> unit
                    </div>
                </div>
            </div>

            <!-- Tabel Histori -->
            <div class="table-responsive">
                <table class="table table-striped" id="tableHistory">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>SKU</th>
                            <th>Tipe</th>
                            <th>Sebelum</th>
                            <th>Perubahan</th>
                            <th>Sesudah</th>
                            <th>Aktivitas</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($log as $item): ?>
                            <tr>
                                <td><?= date('d-m-Y H:i:s', strtotime($item['created_at'])) ?></td>
                                <td><?= $item['nama_barang'] ?></td>
                                <td><?= $item['sku'] ?></td>
                                <td>
                                    <?php 
                                    $badge = $item['tipe_ref'] == 'pembelian' ? 'success' : ($item['tipe_ref'] == 'penjualan' ? 'danger' : 'warning');
                                    $label = $item['tipe_ref'] == 'pembelian' ? 'Masuk' : ($item['tipe_ref'] == 'penjualan' ? 'Keluar' : 'Penyesuaian');
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                                </td>
                                <td><?= number_format($item['jumlah_sebelum']) ?></td>
                                <td class="<?= $item['jumlah_perubahan'] > 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $item['jumlah_perubahan'] > 0 ? '+' : '' ?><?= number_format($item['jumlah_perubahan']) ?>
                                </td>
                                <td><?= number_format($item['jumlah_sesudah']) ?></td>
                                <td><?= $item['aktivitas'] ?></td>
                                <td><?= $item['username'] ?? '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($log)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tableHistory').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
</script>
<?= $this->endSection() ?>