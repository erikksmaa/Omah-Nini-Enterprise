<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<meta name="csrf-token-name" content="<?= csrf_token() ?>">
<meta name="csrf-token-hash" content="<?= csrf_hash() ?>">

<div class="container-fluid px-2 px-md-3">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-database"></i> <?= $title ?>
                    </h6>
                </div>
                <div class="card-body p-2 p-md-3">

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Informasi Backup</strong><br>
                        Fitur ini akan mengekspor seluruh struktur dan data database ke dalam file SQL.
                        File backup dapat digunakan untuk restore data jika terjadi masalah.
                    </div>

                    <!-- Pilihan Tabel -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="selectAll" checked>
                                <label class="form-check-label fw-bold" for="selectAll">
                                    Pilih Semua Tabel
                                </label>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($tables as $table): ?>
                                    <div class="col-md-3 col-6 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input table-checkbox" 
                                                   name="tables[]" value="<?= $table ?>" id="table_<?= $table ?>" checked>
                                            <label class="form-check-label" for="table_<?= $table ?>">
                                                <?= $table ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <button type="button" class="btn btn-success" id="btnExport">
                                <i class="bi bi-download"></i> Export Backup
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnReset">
                                <i class="bi bi-arrow-repeat"></i> Reset Pilihan
                            </button>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-shield-check"></i> File akan diunduh dalam format .SQL
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="alert alert-secondary">
                        <i class="bi bi-table"></i>
                        <strong>Jumlah Tabel:</strong> <?= count($tables) ?> tabel
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Tips:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Backup disarankan dilakukan secara rutin (mingguan/bulanan)</li>
                            <li>Simpan file backup di tempat yang aman (cloud, harddisk eksternal)</li>
                            <li>Untuk restore data, gunakan phpMyAdmin atau import SQL melalui CLI</li>
                            <li>Backup berisi semua data, termasuk data transaksi dan stok</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Select/Deselect all tables
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.table-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update selectAll checkbox when individual checkboxes change
    document.querySelectorAll('.table-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allCheckboxes = document.querySelectorAll('.table-checkbox');
            const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
            document.getElementById('selectAll').checked = allChecked;
        });
    });

    // Reset all checkboxes to checked
    document.getElementById('btnReset').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.table-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        document.getElementById('selectAll').checked = true;
    });

    // Export dengan AJAX (tanpa reload)
    document.getElementById('btnExport').addEventListener('click', function(e) {
        const selectedCount = document.querySelectorAll('.table-checkbox:checked').length;
        
        if (selectedCount === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal satu tabel untuk dibackup!',
                confirmButtonColor: '#f6c23e'
            });
            return;
        }
        
        // Ambil nilai checkbox yang dipilih
        const selectedTables = [];
        document.querySelectorAll('.table-checkbox:checked').forEach(checkbox => {
            selectedTables.push(checkbox.value);
        });
        
        Swal.fire({
            title: 'Konfirmasi Backup',
            html: `Anda akan membackup <strong>${selectedTables.length}</strong> tabel.<br>Proses ini mungkin memakan waktu. Lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Backup!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses Backup...',
                    text: 'Mohon tunggu, sedang mengekspor database.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Kirim request AJAX
                fetch('<?= base_url("admin/backup/export") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify({
                        tables: selectedTables
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.blob();
                })
                .then(blob => {
                    // Buat URL untuk download
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'backup_batik_<?= date('Y-m-d_H-i-s') ?>.sql';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    
                    // Tutup loading dan tampilkan sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Backup Berhasil!',
                        text: 'File backup telah diunduh.',
                        confirmButtonColor: '#28a745',
                        timer: 3000
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Backup Gagal',
                        text: 'Terjadi kesalahan saat membackup database.',
                        confirmButtonColor: '#d33'
                    });
                });
            }
        });
    });
</script>

<style>
    .form-check-input:checked {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    .table-checkbox {
        cursor: pointer;
    }
</style>

<?= $this->endSection() ?>