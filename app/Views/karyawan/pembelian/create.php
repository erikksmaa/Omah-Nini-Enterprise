<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4>Tambah Barang Masuk</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('karyawan/pembelian/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">No. Invoice</label>
                    <input type="text" name="no_invoice" class="form-control" value="<?= $no_invoice ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Supplier / Brand</label>
                    <select name="id_supplier" class="form-select" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id'] ?>"><?= esc($sup['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="date" name="tanggal_pembelian" class="form-control" value="<?= date('Y-m-d') ?>"
                        required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <hr>
            <h5>Item Produk</h5>
            <div id="items-container"></div>

            <button type="button" id="btnAddItem" class="btn btn-outline-primary mb-3">
                <i class="bi bi-plus"></i> Tambah Produk
            </button>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">Simpan Barang Masuk</button>
                <a href="<?= base_url('karyawan/pembelian') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<template id="itemTemplate">
    <div class="row item-row mb-2 align-items-end border-bottom pb-2">
        <div class="col-md-7">
            <label class="form-label">Produk</label>
            <select name="items[INDEX][id_produk]" class="form-select produk-select" style="width: 100%;" required>
                <option value="">-- Cari Produk --</option>
            </select>
            <div class="mt-2 foto-preview" style="display: none;">
                <img class="foto-img" src="" alt="Foto Produk"
                    style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; cursor: pointer;">
                <span class="small text-muted ms-2">Klik gambar untuk memperbesar</span>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Jumlah</label>
            <input type="number" name="items[INDEX][jumlah]" class="form-control jumlah" min="1" value="1" required>
        </div>
        <div class="col-md-2 text-end">
            <button type="button" class="btn btn-danger btn-remove-item"><i class="bi bi-trash"></i> Hapus</button>
        </div>
    </div>
</template>

<!-- Modal Zoom -->
<div class="modal fade" id="zoomModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">Foto Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="zoomImage" src="" style="max-width: 100%; max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('items-container');
        const template = document.getElementById('itemTemplate');
        let itemIndex = 0;
        let productFotoMap = new Map();

        function formatOption(option) {
            if (!option.id) return option.text;
            return option.text;
        }

        function initSelect2(element, previewContainer, fotoImg) {
            $(element).select2({
                placeholder: "-- Cari Produk --",
                allowClear: true,
                width: '100%',
                templateResult: formatOption,
                templateSelection: formatOption,
                ajax: {
                    url: '<?= base_url("api/search/produk") ?>',
                    dataType: 'json',
                    delay: 300,
                    type: 'GET',
                    data: function (params) {
                        return { keyword: params.term };
                    },
                    processResults: function (data) {
                        console.log('Data dari API:', data); // Debug
                        data.forEach(function (item) {
                            productFotoMap.set(parseInt(item.id), item.foto);
                            console.log('ID:', item.id, 'Foto:', item.foto); // Debug
                        });
                        return {
                            results: data.map(function (item) {
                                return { id: parseInt(item.id), text: item.text };
                            })
                        };
                    }
                },
                minimumInputLength: 2
            });

            // Event saat produk dipilih
            $(element).on('change', function() {
                const selectedId = parseInt($(this).val());
                const foto = productFotoMap.get(selectedId);
                
                console.log('Selected ID:', selectedId);
                console.log('Foto URL:', foto);
                console.log('Preview Container:', previewContainer);
                console.log('Foto Img:', fotoImg);
                
                if (foto && fotoImg && previewContainer) {
                    // Set src foto
                    fotoImg.src = foto;
                    // Tampilkan preview
                    previewContainer.style.display = 'block';
                    previewContainer.style.display = 'flex';
                    console.log('Foto ditampilkan');
                } else {
                    previewContainer.style.display = 'none';
                    fotoImg.src = '';
                    console.log('Foto tidak ditampilkan');
                }
            });
        }

        function addItem() {
            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('.item-row');
            row.innerHTML = row.innerHTML.replace(/INDEX/g, itemIndex);

            const select = row.querySelector('.produk-select');
            const preview = row.querySelector('.foto-preview');
            const fotoImg = row.querySelector('.foto-img');

            container.appendChild(row);

            initSelect2(select, preview, fotoImg);

            // Zoom click
            fotoImg.addEventListener('click', function () {
                const src = this.src;
                if (src && !src.includes('no-image')) {
                    document.getElementById('zoomImage').src = src;
                    const zoomModal = new bootstrap.Modal(document.getElementById('zoomModal'));
                    zoomModal.show();
                }
            });

            // Remove item
            row.querySelector('.btn-remove-item').addEventListener('click', function () {
                $(select).select2('destroy');
                row.remove();
            });

            itemIndex++;
        }

        // Tombol tambah item
        document.getElementById('btnAddItem').addEventListener('click', addItem);

        // Tambah item pertama jika kosong
        if (container.children.length === 0) {
            addItem();
        }
    });
</script>
<style>
    .foto-preview {
        margin-top: 8px;
        display: none;  /* default hidden */
        align-items: center;
        gap: 8px;
    }
    
    .foto-preview.show {
        display: flex !important;
    }
    
    .foto-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
        cursor: pointer;
    }
</style>
<?= $this->endSection() ?>