<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | Syarifa Batik Inventory</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app-mod.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app-dark.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/icon-ly.css') ?>">

</head>

<body>
    <div id="app">
        <?= $this->include('layout/sidebar') ?>

        <div id="main">
            <header class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="#" class="burger-btn d-block d-xl-none">
                        <i class="bi bi-justify fs-3"></i>
                    </a>
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <?= session()->get('username') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">
                                    <i class="bi bi-person-badge"></i> Role: <?= session()->get('role') ?>
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="#" id="btnLogoutNavbar">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <div class="page-heading">
                <h3><?= $title ?></h3>
            </div>

            <div class="page-content">
                <?= $this->renderSection('content') ?>
            </div>

            <!-- Ganti footer -->
<footer>
    <div class="footer clearfix mb-0 text-muted">
        <div class="float-start">
            <p>2026 &copy; Syarifa Batik Inventory</p>
        </div>
        <div class="float-end">
            <p>Crafted by <span class="text-danger">ER Team</span></p>
        </div>
    </div>
</footer>
        </div>
    </div>

    <script src="<?= base_url('assets/static/js/components/dark.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>


    <script src="<?= base_url('jQuery-Mask-Plugin-master/dist/jquery.mask.min.js') ?>"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SweetAlert untuk Flashdata -->
    <script>
        <?php if (session()->getFlashdata('success')): ?>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: '<?= session()->getFlashdata('success') ?>'
            });
        <?php endif; ?>

        // Error notification
        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?= session()->getFlashdata('error') ?>',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        // SweetAlert untuk flashdata errors (multiple validation errors)
        <?php if (session()->getFlashdata('errors')): ?>
            <?php
            $errors = session()->getFlashdata('errors');
            $errorText = '';
            if (is_array($errors)) {
                $errorText = implode('\n', $errors);
            } else {
                $errorText = $errors;
            }
            ?>
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                html: '<?= str_replace("\n", '<br>', $errorText) ?>',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        // Warning notification
        <?php if (session()->getFlashdata('warning')): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: '<?= session()->getFlashdata('warning') ?>',
                confirmButtonColor: '#f59e0b'
            });
        <?php endif; ?>

        // Info notification
        <?php if (session()->getFlashdata('info')): ?>
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: '<?= session()->getFlashdata('info') ?>',
                confirmButtonColor: '#3b82f6'
            });
        <?php endif; ?>

        <?php if (session()->get('login_message')): ?>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: '<?= addslashes(session()->get('login_message')) ?>'
            });
            <?php session()->remove('login_message'); ?>
        <?php endif; ?>

        // SweetAlert untuk Logout dari Navbar
        const btnLogoutNavbar = document.getElementById('btnLogoutNavbar');
        if (btnLogoutNavbar) {
            btnLogoutNavbar.addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin logout?',
                    text: "Anda akan keluar dari sistem.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Logout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?= base_url('/logout') ?>';
                    }
                });
            });
        }

        // SweetAlert untuk Logout dari Sidebar
        const btnLogoutSidebar = document.getElementById('btnLogoutSidebar');
        if (btnLogoutSidebar) {
            btnLogoutSidebar.addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin logout?',
                    text: "Anda akan keluar dari sistem.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Logout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?= base_url('/logout') ?>';
                    }
                });
            });
        }

        function confirmDelete(url, itemName) {
            Swal.fire({
                title: 'Yakin hapus?',
                text: `Data "${itemName}" akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
            return false;
        }
    </script>
</body>

</html>