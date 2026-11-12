
<?php
require_once __DIR__ . '/config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika belum login, tampilkan halaman login
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Jika sudah login sebagai admin
if ($_SESSION['role'] === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

// Jika sudah login sebagai pemilih
if ($_SESSION['role'] === 'pemilih') {
    header('Location: pages/index.php');
    exit;
}

// Jika role tidak dikenali
session_destroy();
header('Location: auth/login.php');
exit;
