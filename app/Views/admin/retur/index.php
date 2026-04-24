<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-arrow-return-left"></i> Retur Penjualan</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="<?= base_url('admin/retur/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Retur Baru
                </a>
            </div>
            <!-- Filter Form -->
            <form method="GET" class="row mb-3">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Cari No Retur / No Invoice..."
                        value="<?= esc($keyword ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" name="start_date" class="form-control" placeholder="Start Date"
                        value="<?= esc($start_date ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" name="end_date" class="form-control" placeholder="End Date"
                        value="<?= esc($end_date ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>

            <!-- Reset Filter -->
            <?php if (!empty($keyword) || !empty($start_date) || !empty($end_date)): ?>
                <div class="mb-3">
                    <a href="<?= base_url('admin/retur') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Reset Filter
                    </a>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped" id="tableRetur">
                    <thead class="table-dark">
                        <tr>
                            <th>No Retur</th>
                            <th>No Invoice</th>
                            <th>Tanggal Retur</th>
                            <th>Total Retur</th>
                            <th>Alasan</th>
                            <th>User</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($retur) && is_array($retur)): ?>
                            <?php foreach ($retur as $item): ?>
                                <tr>
                                    <td><?= esc($item['no_retur']) ?></td>
                                    <td><?= esc($item['no_invoice']) ?></td>
                                    <td><?= date('d-m-Y', strtotime($item['tanggal_retur'])) ?></td>
                                    <td>Rp <?= number_format($item['total_retur'], 0, ',', '.') ?></td>
                                    <td><?= esc($item['alasan']) ?></td>
                                    <td><?= esc($item['username'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/retur/detail/' . $item['id']) ?>"
                                            class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <?php
                                    if (!empty($keyword) || !empty($start_date) || !empty($end_date)) {
                                        echo 'Tidak ada data retur yang sesuai dengan filter';
                                    } else {
                                        echo 'Belum ada data retur';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($pager) && $pager && $pager->getTotal() > 0): ?>
                <div class="mt-4 d-flex justify-content-center">
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
                <div class="text-center text-muted small mt-2">
                    Menampilkan <?= count($retur) ?> dari <?= $pager->getTotal() ?> data
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#tableRetur').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            paging: false,
            searching: false,
            ordering: false,
            info: false
        });
    });
</script>
<?= $this->endSection() ?>