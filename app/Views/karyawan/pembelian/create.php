<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4>Tambah Barang Masuk</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('karyawan/pembelian/store') ?>" method="post" id="form-pembelian">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Merek</label>
                    <select name="id_supplier" id="id_supplier" class="form-select" required>
                        <option value="">-- Pilih Merek --</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id'] ?>"><?= esc($sup['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="date" name="tanggal_pembelian" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <hr>
            <h5>Item Produk</h5>
            <div class="alert alert-info small">
                <i class="bi bi-info-circle"></i> Pilih merek terlebih dahulu, lalu tambahkan produk. Produk yang tampil hanya dari merek tersebut.
            </div>
            <div id="items-container"></div>

            <button type="button" id="btnAddItem" class="btn btn-outline-primary mb-3">
                <i class="bi bi-plus"></i> Tambah Produk
            </button>

            <div class="mt-3">
                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan Barang Masuk</button>
                <a href="<?= base_url('karyawan/pembelian') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<template id="itemTemplate">
    <div class="row item-row mb-2 align-items-end border-bottom pb-2">
        <div class="col-md-8">
            <label class="form-label">Produk</label>
            <select name="items[INDEX][id_produk]" class="form-select produk-select" style="width: 100%;" required>
                <option value="">-- Pilih Merek dulu --</option>
            </select>
            <!-- Preview foto produk -->
            <div class="mt-2 foto-preview" style="display: none;">
                <div class="foto-wrapper" style="display: inline-block; cursor: pointer;">
                    <img class="foto-img" src="" alt="Foto Produk"
                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                    <div class="foto-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s; border-radius: 8px;">
                        <i class="bi bi-zoom-in text-white fs-4"></i>
                    </div>
                </div>
                <span class="small text-muted ms-2">Klik gambar untuk memperbesar</span>
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label">Jumlah</label>
            <input type="number" name="items[INDEX][jumlah]" class="form-control jumlah" min="1" value="1" required>
        </div>
        <div class="col-md-2 text-end">
            <button type="button" class="btn btn-danger btn-remove-item mt-4"><i class="bi bi-trash"></i> Hapus</button>
        </div>
    </div>
</template>

<!-- Modal Zoom (Pastikan ID nya fotoModal) -->
<div id="fotoModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">Foto Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="fotoModalImg" src="" style="max-width: 100%; max-height: 70vh;">
                <p id="fotoModalCaption" class="mt-2 text-muted"></p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplierSelect = document.getElementById('id_supplier');
    const container = document.getElementById('items-container');
    const template = document.getElementById('itemTemplate');
    let itemIndex = 0;
    let produkDataMap = new Map();
    let cachedProdukList = [];

    // ===== FUNGSI MODAL FOTO =====
    function showFotoModal(imgSrc, caption) {
        const modalEl = document.getElementById('fotoModal');
        const imgEl = document.getElementById('fotoModalImg');
        const captionEl = document.getElementById('fotoModalCaption');
        
        if (imgEl) imgEl.src = imgSrc;
        if (captionEl) captionEl.innerText = caption || 'Foto Produk';
        
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    // ===== LOAD PRODUK =====
    function loadProdukBySupplier(supplierId, callback) {
        if (!supplierId) { callback([]); return; }

        fetch(`<?= base_url('api/produk/by-supplier/') ?>${supplierId}`)
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    produkDataMap.clear();
                    cachedProdukList = data.data;
                    data.data.forEach(p => {
                        produkDataMap.set(String(p.id), {
                            id: String(p.id),
                            text: `${p.nama_motif} - ${p.nama_warna} (Stok: ${p.stok})`,
                            foto: p.foto || '<?= base_url('assets/img/no-image.png') ?>'
                        });
                    });
                    callback(data.data);
                } else {
                    callback([]);
                }
            })
            .catch(err => { console.error(err); callback([]); });
    }

    // ===== INIT SELECT2 =====
    function initSelect2(selectEl, previewEl, fotoImgEl, produkList) {
        if (!selectEl) return;

        if ($(selectEl).data('select2')) {
            $(selectEl).select2('destroy');
        }

        const options = produkList.map(p =>
            `<option value="${p.id}">${p.nama_motif} - ${p.nama_warna} (Stok: ${p.stok})</option>`
        ).join('');

        $(selectEl).html(`<option value="">-- Cari Produk (Motif - Warna) --</option>${options}`);

        $(selectEl).select2({
            placeholder: '-- Cari Produk (Motif - Warna) --',
            allowClear: true,
            width: '100%',
            language: { noResults: function() { return 'Produk tidak ditemukan'; } }
        });

        // Event change
        $(selectEl).off('change.foto').on('change.foto', function() {
            const id = String($(this).val());
            const produk = produkDataMap.get(id);

            if (produk && produk.foto && !produk.foto.includes('no-image.png')) {
                $(fotoImgEl).attr('src', produk.foto);
                $(previewEl).show();
                // Setup click untuk zoom
                const fotoWrapper = $(previewEl).find('.foto-wrapper');
                fotoWrapper.off('click');
                fotoWrapper.on('click', function(e) {
                    e.stopPropagation();
                    showFotoModal(produk.foto, produk.text);
                });
                // Hover effect
                fotoWrapper.on('mouseenter', function() {
                    $(this).find('.foto-overlay').css('opacity', '1');
                });
                fotoWrapper.on('mouseleave', function() {
                    $(this).find('.foto-overlay').css('opacity', '0');
                });
            } else {
                $(previewEl).hide();
                $(fotoImgEl).attr('src', '');
            }
        });
    }

    // ===== REFRESH SEMUA SELECT =====
    function refreshAllSelects(produkList) {
        document.querySelectorAll('.item-row').forEach(row => {
            const sel = row.querySelector('.produk-select');
            const preview = row.querySelector('.foto-preview');
            const img = row.querySelector('.foto-img');
            if (sel) initSelect2(sel, preview, img, produkList);
        });
    }

    // ===== TAMBAH BARIS ITEM =====
    function addItem() {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.item-row');
        row.innerHTML = row.innerHTML.replace(/INDEX/g, itemIndex);

        const sel = row.querySelector('.produk-select');
        const preview = row.querySelector('.foto-preview');
        const img = row.querySelector('.foto-img');

        container.appendChild(row);

        if (cachedProdukList.length > 0) {
            initSelect2(sel, preview, img, cachedProdukList);
        } else {
            $(sel).select2({
                placeholder: '-- Pilih Merek dulu --',
                allowClear: true,
                width: '100%'
            });
        }

        row.querySelector('.btn-remove-item').addEventListener('click', function() {
            if ($(sel).data('select2')) $(sel).select2('destroy');
            row.remove();
        });

        itemIndex++;
    }

    // ===== EVENT SUPPLIER CHANGE =====
    supplierSelect.addEventListener('change', function() {
        cachedProdukList = [];
        produkDataMap.clear();

        const supplierId = this.value;
        if (!supplierId) {
            document.querySelectorAll('.item-row').forEach(row => {
                const sel = row.querySelector('.produk-select');
                const preview = row.querySelector('.foto-preview');
                const img = row.querySelector('.foto-img');
                if ($(sel).data('select2')) $(sel).select2('destroy');
                $(sel).html('<option value="">-- Pilih Merek dulu --</option>').select2({
                    placeholder: '-- Pilih Merek dulu --',
                    allowClear: true,
                    width: '100%'
                });
                $(preview).hide();
                $(img).attr('src', '');
            });
            return;
        }

        loadProdukBySupplier(supplierId, function(produkList) {
            refreshAllSelects(produkList);
        });
    });

    // ===== TOMBOL TAMBAH =====
    document.getElementById('btnAddItem').addEventListener('click', addItem);

    // Baris pertama
    addItem();
});
</script>

<style>
    .foto-preview {
        margin-top: 8px;
        position: relative;
    }
    .foto-wrapper {
        position: relative;
        display: inline-block;
        cursor: pointer;
        border-radius: 8px;
        overflow: hidden;
    }
    .foto-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
        border-radius: 8px;
    }
    .foto-wrapper:hover .foto-overlay {
        opacity: 1;
    }
    .foto-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
</style>

<?= $this->endSection() ?>