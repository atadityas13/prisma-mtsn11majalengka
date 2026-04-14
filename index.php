<?php
require_once 'includes/config.php';
require_once 'includes/Auth.php';

// Redirect to installer if not installed
if (!file_exists('includes/config.php') || !file_exists('install.lock')) {
    header("Location: install.php");
    exit;
}

if (Auth::isLoggedIn()) {
    if ($_SESSION['role'] === 'admin') header("Location: " . base_url("admin/dashboard.php"));
    elseif ($_SESSION['role'] === 'guru') header("Location: " . base_url("guru/dashboard.php"));
    else header("Location: " . base_url("siswa/dashboard.php"));
} else {
    header("Location: " . base_url("login.php"));
}
exit;
