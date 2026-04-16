<?php
// Redirect to installer if not configured
if (!file_exists('includes/config.php')) {
    header("Location: install.php");
    exit;
}

require_once 'includes/config.php';
require_once 'includes/Auth.php';

$auth = new Auth();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($auth->login($username, $password)) {
        // Redirect based on role
        if ($_SESSION['role'] === 'admin') {
            header("Location: " . base_url("admin/dashboard.php"));
        } elseif ($_SESSION['role'] === 'guru') {
            header("Location: " . base_url("guru/dashboard.php"));
        } else {
            header("Location: " . base_url("siswa/dashboard.php"));
        }
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}

// Redirect if already logged in
if (Auth::isLoggedIn()) {
    if ($_SESSION['role'] === 'admin')
        header("Location: " . base_url("admin/dashboard.php"));
    elseif ($_SESSION['role'] === 'guru')
        header("Location: " . base_url("guru/dashboard.php"));
    else
        header("Location: " . base_url("siswa/dashboard.php"));
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Login | PRISMA - MTsN 11 Majalengka</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo-mtsn11.png') ?>" />
    <link rel="shortcut icon" href="<?= base_url('assets/img/logo-mtsn11.png') ?>" type="image/png" />
    <link rel="apple-touch-icon" href="<?= base_url('assets/img/logo-mtsn11.png') ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />

    <style>
        body {
            background-color: #f5f5f9;
            font-family: 'Public Sans', sans-serif;
        }

        .authentication-wrapper {
            display: flex;
            flex-basis: 100%;
            min-height: 100vh;
            width: 100%;
            align-items: center;
            justify-content: center;
        }

        .authentication-inner {
            max-width: 400px;
            width: 100%;
            padding: 1.5rem;
        }

        .card {
            border: none;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
            border-radius: 0.5rem;
        }

        .app-brand {
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .app-brand-logo {
            background-color: #696cff;
            color: #fff;
            width: 34px;
            height: 34px;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
        }

        .btn-primary {
            background-color: #696cff;
            border-color: #696cff;
            box-shadow: 0 0.125rem 0.25rem 0 rgba(105, 108, 255, 0.4);
        }

        .btn-primary:hover {
            background-color: #5f61e6;
            border-color: #5f61e6;
        }

        .form-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 500;
            color: #566a7f;
        }
    </style>
</head>

<body>
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card">
                <div class="card-body">
                    <!-- Brand -->
                    <div class="app-brand mb-4">
                        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo PRISMA" height="160">
                    </div>
                    <!-- /Brand -->
                    <h4 class="mb-2">Assalamu'alaikum!</h4>
                    <p class="mb-4">Silakan login dengan akun yang diberikan.</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form id="formAuthentication" class="mb-3" action="" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Masukkan username Anda" autofocus required />
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Password</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="············" required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Masuk</button>
                        </div>
                    </form>

                    <p class="text-center">
                        <span>Lupa password? Hubungi Admin</span>
                    </p>
                </div>
            </div>
            <div class="text-center mt-3 text-muted" style="font-size: 0.85rem;">
                &copy; 2026 <?= SCHOOL_NAME ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>