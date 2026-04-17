<?= $this->extend('layout/header') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-journal-text"></i> Audit Trail - Log Stok</h5>
    </div>
    <div class="card-body p-3">
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label>Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?? date('Y-m-01') ?>">
                </div>
                <div class="col-md-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?? date('Y-m-t') ?>">
                </div>
                <div class="col-md-3">
                    <label>Produk</label>
                    <select name="produk_id" class="form-control">
                        <option value="">Semua Produk</option>
                        <?php foreach ($produk as $item): ?>
                            <option value="<?= $item['id'] ?>" <?= ($selected_produk ?? '') == $item['id'] ? 'selected' : '' ?>>
                                <?= $item['sku'] ?> - <?= $item['nama_barang'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-striped" id="tableLogStok">
                <thead class="table-dark">
                    <tr>
                        <th>Waktu</th>
                        <th>Produk</th>
                        <th>Aktivitas</th>
                        <th>Perubahan</th>
                        <th>Stok Sebelum</th>
                        <th>Stok Sesudah</th>
                        <th>Referensi</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($log_stok as $item): ?>
                    <tr>
                        <td><?= date('d-m-Y H:i:s', strtotime($item['created_at'])) ?></td>
                        <td>
                            <strong><?= $item['nama_barang'] ?></strong><br>
                            <small class="text-muted"><?= $item['sku'] ?></small>
                        </td>
                        <td>
                            <span class="badge bg-<?= $item['aktivitas'] == 'IN' ? 'success' : ($item['aktivitas'] == 'OUT' ? 'danger' : 'warning') ?>">
                                <?= $item['aktivitas'] == 'IN' ? 'Barang Masuk' : ($item['aktivitas'] == 'OUT' ? 'Barang Keluar' : 'Stok Opname') ?>
                            </span>
                        </td>
                        <td>
                            <span class="<?= $item['jumlah_perubahan'] > 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                <?= $item['jumlah_perubahan'] > 0 ? '+' : '' ?><?= $item['jumlah_perubahan'] ?>
                            </span>
                        </td>
                        <td><?= $item['jumlah_sebelum'] ?></td>
                        <td><?= $item['jumlah_sesudah'] ?></                        </td>
                        <td>
                            <?php 
                            $refText = '';
                            if ($item['tipe_ref'] == 'pembelian') {
                                $refText = 'PO #' . $item['id_ref'];
                            } elseif ($item['tipe_ref'] == 'penjualan') {
                                $refText = 'INV #' . $item['id_ref'];
                            } elseif ($item['tipe_ref'] == 'opname') {
                                $refText = 'Opname Manual';
                            } elseif ($item['tipe_ref'] == 'retur') {
                                $refText = 'Retur #' . $item['id_ref'];
                            } else {
                                $refText = $item['tipe_ref'] . ' #' . $item['id_ref'];
                            }
                            ?>
                            <?= $refText ?>
                        </td>
                        <td><?= $item['username'] ?? '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tableLogStok').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
<?= $this->endSection() ?>