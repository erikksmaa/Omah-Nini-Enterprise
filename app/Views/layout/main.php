<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | Omah Nini Enterprise</title>

    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app-dark.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/icon-ly.css') ?>">
</head>

<body>
    <!-- Di bagian kanan navbar -->
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i>
            <?= session()->get('username') ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">
                    <i class="bi bi-person-badge"></i> Role:
                    <?= session()->get('role') ?>
                </a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="<?= base_url('/logout') ?>"
                    onclick="return confirm('Yakin ingin logout?')">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a></li>
        </ul>
    </li>

    <div id="app">
        <?= $this->include('layout/sidebar') ?>

        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3><?= $title ?></h3>
            </div>

            <div class="page-content">
                <?= $this->renderSection('content') ?>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2026 &copy; Omah Nini Enterprise</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted by <span class="text-danger">ER</span></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="<?= base_url('assets/static/js/components/dark.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>
</body>

</html>