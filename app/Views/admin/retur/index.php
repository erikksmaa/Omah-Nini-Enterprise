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

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
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
                        <?php foreach ($retur as $item): ?>
                            <tr>
                                <td><?= $item['no_retur'] ?></td>
                                <td><?= $item['no_invoice'] ?></td>
                                <td><?= date('d-m-Y', strtotime($item['tanggal_retur'])) ?></td>
                                <td>Rp <?= number_format($item['total_retur'], 0, ',', '.') ?></td>
                                <td><?= $item['alasan'] ?></td>
                                <td><?= $item['username'] ?? '-' ?></td>
                                <td>
                                    <a href="<?= base_url('admin/retur/detail/' . $item['id']) ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <a href="<?= base_url('admin/retur/delete/' . $item['id']) ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin batalkan retur ini? Stok akan dikembalikan ke semula.')">
                                        <i class="bi bi-x-circle"></i> Batal
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($retur)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data retur</td>
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
        $('#tableRetur').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[2, 'desc']],
            pageLength: 25
        });
    });
</script>
<?= $this->endSection() ?>