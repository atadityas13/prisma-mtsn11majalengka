<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/Auth.php';

Auth::restrictTo('siswa');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Siswa | PRISMA</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo-mtsn11.png') ?>" />
    <link rel="shortcut icon" href="<?= base_url('assets/img/logo-mtsn11.png') ?>" type="image/png" />
    <link rel="apple-touch-icon" href="<?= base_url('assets/img/logo-mtsn11.png') ?>" />

    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f9; font-family: 'Public Sans', sans-serif; }
        .card { border: none; box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12); border-radius: 0.5rem; margin-bottom: 20px; }
        .navbar { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); box-shadow: 0 0.125rem 0.375rem 0 rgba(161, 172, 184, 0.12); border-bottom: 1px solid #eee; }
        .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; font-weight: 600; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top py-2 px-0">
        <div class="container px-3">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="<?= base_url('assets/img/logo-mtsn11.png') ?>" alt="Logo" height="40" class="me-3">
                <span class="fw-bold text-dark d-none d-md-inline-block me-3" style="font-size: 1.1rem;">PRISMA <span class="text-muted fw-normal d-none d-lg-inline">(Penilaian Ujian Praktik Siswa)</span></span>
            </a>
            <div class="d-flex align-items-center ms-auto">
                <span class="badge bg-label-primary px-3 py-2 me-3">
                     <i class="bx bx-calendar me-1"></i> TA: <?= DEFAULT_YEAR ?>
                </span>
                <a href="<?= base_url('logout.php') ?>" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 80px;">
