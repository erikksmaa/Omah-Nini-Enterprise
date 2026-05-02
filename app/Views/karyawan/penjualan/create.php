<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4>Barang Keluar (POS)</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('karyawan/penjualan/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">No. Invoice</label>
                    <input type="text" name="no_invoice" class="form-control" value="<?= old('no_invoice', $no_invoice) ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="datetime-local" name="tanggal_transaksi" class="form-control" value="<?= old('tanggal_transaksi', date('Y-m-d\TH:i')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pelanggan (opsional)</label>
                    <select name="id_pelanggan" class="form-select" id="pelanggan">
                        <option value="">-- Umum / Tidak Ada --</option>
                        <?php foreach ($pelanggan_list as $pel): ?>
                            <option value="<?= $pel['id'] ?>" data-nama="<?= esc($pel['nama']) ?>" <?= old('id_pelanggan') == $pel['id'] ? 'selected' : '' ?>>
                                <?= esc($pel['nama']) ?> - <?= esc($pel['no_telp'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Pembeli</label>
                    <input type="text" name="nama_pembeli" id="nama_pembeli" class="form-control" value="<?= old('nama_pembeli') ?>" placeholder="Nama pembeli" required>
                    <small class="text-muted">Jika pelanggan dipilih, nama pembeli akan otomatis terisi dan tidak dapat diubah.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"><?= old('catatan') ?></textarea>
                </div>
            </div>

            <hr>
            <h5>Item Produk</h5>
            <div id="items-container">
                <!-- JS akan mengisi baris item -->
            </div>

            <button type="button" id="btnAddItem" class="btn btn-outline-primary mb-3">
                <i class="bi bi-plus"></i> Tambah Produk
            </button>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-cart-check"></i> Proses Penjualan
                </button>
                <a href="<?= base_url('karyawan/penjualan') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<template id="itemTemplate">
    <div class="row item-row mb-2 align-items-end">
        <div class="col-md-8">
            <label class="form-label">Produk</label>
            <select name="items[INDEX][id_produk]" class="form-select produk-select" style="width: 100%;" required>
                <option value="">-- Cari Produk (SKU / Nama) --</option>
                <?php foreach ($produk_list as $prod): ?>
                    <option value="<?= $prod['id'] ?>">
                        <?= esc($prod['sku'] . ' - ' . $prod['nama_motif'] . ' ' . $prod['nama_warna']) ?> (Stok: <?= $prod['stok'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Jumlah</label>
            <input type="number" name="items[INDEX][jumlah]" class="form-control jumlah" placeholder="Jumlah" min="1" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-remove-item"><i class="bi bi-trash"></i></button>
        </div>
    </div>
</template>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    const template = document.getElementById('itemTemplate');
    let itemIndex = 0;

    function initSelect2(element) {
        $(element).select2({
            placeholder: "-- Cari Produk (SKU / Nama) --",
            allowClear: true,
            width: 'resolve'
        });
    }

    function addItem() {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.item-row');
        row.innerHTML = row.innerHTML.replace(/INDEX/g, itemIndex);

        const select = row.querySelector('.produk-select');
        container.appendChild(row);
        initSelect2(select);

        row.querySelector('.btn-remove-item').addEventListener('click', function() {
            $(select).select2('destroy');
            row.remove();
        });

        itemIndex++;
    }

    document.getElementById('btnAddItem').addEventListener('click', addItem);

    if (container.children.length === 0) {
        addItem();
    }

    // ========== LOGIKA PELANGGAN -> NAMA PEMBELI ==========
    const pelangganSelect = document.getElementById('pelanggan');
    const namaPembeliInput = document.getElementById('nama_pembeli');

    function syncNamaPembeli() {
        const selectedOption = pelangganSelect.options[pelangganSelect.selectedIndex];
        const namaPelanggan = selectedOption.getAttribute('data-nama');

        if (namaPelanggan) {
            namaPembeliInput.value = namaPelanggan;
            namaPembeliInput.setAttribute('readonly', true);
        } else {
            namaPembeliInput.value = '';
            namaPembeliInput.removeAttribute('readonly');
        }
    }

    // Event change pada dropdown pelanggan
    pelangganSelect.addEventListener('change', syncNamaPembeli);

    // Jalankan saat halaman dimuat (misal ada old value pelanggan dari sebelumnya)
    syncNamaPembeli();
});
</script>
<?= $this->endSection() ?>