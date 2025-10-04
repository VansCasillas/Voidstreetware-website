<?php
// Mulai session (cek dulu biar ga error double start)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database
$koneksi = mysqli_connect('localhost', 'root', '', 'voidstreetware');

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi proteksi login umum (user / admin)
function cek_login() {
    if (!isset($_SESSION['user']) && !isset($_SESSION['admin'])) {
        header("location: login.php");
        exit;
    }
}

// Fungsi proteksi khusus user
function cek_user() {
    if (!isset($_SESSION['user'])) {
        header("location: login.php");
        exit;
    }
}

// Fungsi proteksi khusus admin
function cek_admin() {
    if (!isset($_SESSION['admin'])) {
        header("location: login.php");
        exit;
    }
}
?>
