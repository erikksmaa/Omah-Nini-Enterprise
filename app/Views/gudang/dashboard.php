<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-cart-plus"></i> Data Pembelian Barang</h5>
        </div>
        <div class="card-body p-3">
            <div class="mb-3">
                <a href="<?= base_url('gudang/pembelian/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Pembelian Baru
                </a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped" id="tablePembelian">
                    <thead class="table-dark">
                        <tr>
                            <th>No Invoice</th>
                            <th>Supplier</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pembelian as $item): ?>
                            <tr>
                                <td><?= $item['no_invoice'] ?></td>
                                <td><?= $item['supplier_nama'] ?></td>
                                <td><?= date('d-m-Y', strtotime($item['tanggal_pembelian'])) ?></td>
                                <td>Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                                <td><?= $item['catatan'] ?? '-' ?></td>
                                <td>
                                    <a href="<?= base_url('gudang/pembelian/detail/' . $item['id']) ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($pembelian)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data pembelian</td>
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
        $('#tablePembelian').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            order: [[2, 'desc']]
        });
    });
</script>
<?= $this->endSection() ?>