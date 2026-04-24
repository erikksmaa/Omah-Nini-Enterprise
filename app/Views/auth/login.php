<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Omah Nini Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
        }

        .login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .login-header {
            padding: 40px 32px 24px;
            text-align: center;
            background: #ffffff;
        }

        .login-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #e8f0fe 0%, #d4e4fc 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .login-icon i {
            font-size: 36px;
            color: #4f46e5;
        }

        .login-header h3 {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 0;
        }

        .login-body {
            padding: 0 32px 40px;
        }

        .form-label {
            font-weight: 500;
            font-size: 13px;
            color: #475569;
            margin-bottom: 6px;
        }

        .input-group {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease;
            background: #f8fafc;
        }

        .input-group:focus-within {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            background: #ffffff;
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 10px 0 10px 16px;
        }

        .form-control {
            border: none;
            background: transparent;
            padding: 10px 16px 10px 0;
            font-size: 14px;
            color: #1e293b;
        }

        .form-control:focus {
            outline: none;
            box-shadow: none;
            background: transparent;
        }

        .form-control::placeholder {
            color: #94a3b8;
            font-size: 13px;
        }

        .form-check {
            margin-top: 12px;
        }

        .form-check-input {
            cursor: pointer;
            border-color: #cbd5e1;
        }

        .form-check-input:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .form-check-label {
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
        }

        .btn-login {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            font-size: 14px;
            border-radius: 12px;
            margin-top: 8px;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.3);
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 24px 0 20px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider span {
            padding: 0 12px;
            font-size: 12px;
            color: #94a3b8;
        }

        .demo-accounts {
            background: #f8fafc;
            border-radius: 16px;
            padding: 16px;
            margin-top: 8px;
        }

        .demo-title {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            text-align: center;
        }

        .demo-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }

        .demo-item {
            background: #ffffff;
            border-radius: 10px;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 500;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-family: monospace;
        }

        .demo-item strong {
            color: #4f46e5;
        }

        .alert {
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
        }

        .alert-success {
            background: #ecfdf5;
            color: #059669;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-header {
                padding: 32px 24px 20px;
            }

            .login-body {
                padding: 0 24px 32px;
            }

            .login-icon {
                width: 56px;
                height: 56px;
            }

            .login-icon i {
                font-size: 28px;
            }

            .login-header h3 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="bi bi-shop"></i>
                </div>
                <h3>Omah Nini</h3>
                <p>Enterprise Inventory System</p>
            </div>
            <div class="login-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/login/process') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" id="username" class="form-control"
                                value="<?= old('username') ?>" placeholder="Masukkan username" autofocus required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Masukkan password" required>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="showPassword">
                        <label class="form-check-label" for="showPassword">
                            <i class="bi bi-eye"></i> Lihat Password
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                    </button>
                </form>

                <div class="divider">
                    <span>Demo Account</span>
                </div>

                <div class="demo-accounts">
                    <div class="demo-title">Gunakan akun berikut untuk login</div>
                    <div class="demo-grid">
                        <div class="demo-item"><strong>admin</strong> / admin123</div>
                        <div class="demo-item"><strong>gudang</strong> / admin123</div>
                        <div class="demo-item"><strong>kasir</strong> / admin123</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide password
        document.getElementById('showPassword').addEventListener('change', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.nextElementSibling.querySelector('i');
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Cek parameter URL untuk logout
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.get('logout') === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Anda telah berhasil logout.',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                // Hapus parameter dari URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            // Cek flashdata error
            <?php if (session()->getFlashdata('error')): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal!',
                    text: '<?= addslashes(session()->getFlashdata('error')) ?>',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            <?php endif; ?>
        });
    </script>

</body>

</html>