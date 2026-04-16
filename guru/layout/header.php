<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/Auth.php';

Auth::restrictTo('guru');
?>
<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?= $page_title ?? 'Dashboard' ?> | PRISMA Guru</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo-mtsn11.png') ?>" />
    <link rel="shortcut icon" href="<?= base_url('assets/img/logo-mtsn11.png') ?>" type="image/png" />
    <link rel="apple-touch-icon" href="<?= base_url('assets/img/logo-mtsn11.png') ?>" />

    <!-- Fonts & Icons (Same CDNs as Admin) -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        :root { --primary: #696cff; --light: #f5f5f9; }
        body { background-color: var(--light); font-family: 'Public Sans', sans-serif; margin: 0; }
        .layout-wrapper { display: flex; width: 100%; min-height: 100vh; }
        #layout-menu { width: 260px; background: #fff; box-shadow: 0 0.125rem 0.375rem 0 rgba(161, 172, 184, 0.12); z-index: 10; flex-shrink: 0; height: 100vh; position: sticky; top: 0; }
        .layout-page { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .content-wrapper { flex: 1; padding: 1.5rem; }
        .navbar { position: sticky; top: 0; z-index: 1000; background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(10px); border-bottom: 1px solid #d9dee3; padding: 0.5rem 1.5rem; width: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        /* Menu styles placeholder */
        .menu-inner { padding: 0.5rem 1rem; list-style: none; }
        .menu-link { display: flex; align-items: center; padding: 0.625rem 1rem; color: #697a8d; text-decoration: none; border-radius: 0.375rem; }
        .menu-link:hover, .menu-item.active .menu-link { background: rgba(105, 108, 255, 0.1); color: var(--primary); }
        .menu-link i { margin-right: 0.75rem; }
        .card { border: none; box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12); border-radius: 0.5rem; margin-bottom: 1.5rem; }

        /* Mobile Responsive Sidebar */
        @media (max-width: 1199.98px) {
            #layout-menu {
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                z-index: 1200;
            }
            .layout-menu-expanded #layout-menu {
                transform: translateX(0);
            }
            .layout-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1150;
                display: none;
            }
            .layout-menu-expanded .layout-overlay {
                display: block;
            }
            .layout-page {
                padding-left: 0 !important;
            }
            .content-wrapper {
                padding: 1rem !important;
            }
        }

        /* Toggle Button */
        .layout-menu-toggle {
            padding: 5px;
            margin-right: 15px;
            cursor: pointer;
            display: none;
            color: #697a8d;
        }

        @media (max-width: 1199.98px) {
            .layout-menu-toggle {
                display: inline-block;
            }
        }

        /* High Contrast Labels */
        .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; font-weight: 600; }
        .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; font-weight: 600; }
        .bg-label-danger { background-color: #ffe5e5 !important; color: #ff3e1d !important; font-weight: 600; }
        .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; font-weight: 600; }
        .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; font-weight: 600; }
        .bg-label-secondary { background-color: #ebedef !important; color: #8592a3 !important; font-weight: 600; }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <div class="layout-overlay"></div>
        <aside id="layout-menu" class="menu-vertical menu bg-menu-theme">
            <div class="p-4 text-center">
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" height="85">
            </div>
            <ul class="menu-inner py-1">
                <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">
                    <a href="<?= base_url('guru/dashboard.php') ?>" class="menu-link"><i class="bx bx-home-circle"></i> <div>Dashboard</div></a>
                </li>
                <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'materi.php') ? 'active' : '' ?>">
                    <a href="<?= base_url('guru/materi.php') ?>" class="menu-link"><i class="bx bx-book-content"></i> <div>Materi Uji</div></a>
                </li>
                <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'aspek.php') ? 'active' : '' ?>">
                    <a href="<?= base_url('guru/aspek.php') ?>" class="menu-link"><i class="bx bx-list-check"></i> <div>Aspek Penilaian</div></a>
                </li>
                <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'penilaian.php') ? 'active' : '' ?>">
                    <a href="<?= base_url('guru/penilaian.php') ?>" class="menu-link"><i class="bx bx-edit"></i> <div>Input Nilai</div></a>
                </li>
                <li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'laporan.php') ? 'active' : '' ?>">
                    <a href="<?= base_url('guru/laporan.php') ?>" class="menu-link"><i class="bx bx-file"></i> <div>Laporan Saya</div></a>
                </li>
                <li class="menu-item mt-5">
                    <a href="<?= base_url('logout.php') ?>" class="menu-link text-danger"><i class="bx bx-power-off"></i> <div>Logout</div></a>
                </li>
            </ul>
        </aside>

        <div class="layout-page">
            <nav class="navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme container-xxl mt-2 mx-auto">
                <div class="container-fluid">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" id="mobileMenuToggle">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>
                    
                    <div class="navbar-nav align-items-center">
                        <div class="nav-item d-flex align-items-center">
                            <span class="fw-bold text-dark d-none d-md-inline-block me-3">PRISMA <span class="text-muted fw-normal d-none d-lg-inline">(Penilaian Ujian Praktik Siswa)</span></span>
                            <span class="badge bg-label-primary px-3 py-2">
                                <i class="bx bx-calendar me-2"></i> TA: <?= DEFAULT_YEAR ?>
                            </span>
                        </div>
                    </div>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="text-muted small me-3 d-none d-sm-inline"><strong><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></strong></span>
                        <span class="badge bg-label-info"><?= strtoupper($_SESSION['role']) ?></span>
                    </div>
                </div>
            </nav>

            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
