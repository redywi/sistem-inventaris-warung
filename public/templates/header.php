<?php
// session_start() sudah dipanggil di halaman utama
// Cek apakah pengguna sudah login, jika tidak, redirect ke halaman login
if (!isset($_SESSION["user_id"]) && basename($_SERVER['SCRIPT_NAME']) != 'login.php') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIW - Sistem Inventaris Warung</title>
    <link rel="stylesheet" href="/sistem-inventaris-warung/public/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistem Inventaris Warung (SIW)</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="tambah_penjualan.php">Kasir</a>
                <a href="daftar_penjualan.php">Riwayat Penjualan</a>
                <a href="daftar_barang.php">Manajemen Barang</a>
                <a href="daftar_kategori.php">Manajemen Kategori</a>
                <a href="logout.php" id="logout-link">Logout</a>
            </nav>
        </header>
        <main>
            <?php
            // Menampilkan pesan notifikasi dari session
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">'. $_SESSION['success']. '</div>';
                unset($_SESSION['success']); // Hapus pesan setelah ditampilkan
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">'. $_SESSION['error']. '</div>';
                unset($_SESSION['error']); // Hapus pesan setelah ditampilkan
            }
          ?>
        <script>
        // Konfirmasi sebelum logout
        document.addEventListener('DOMContentLoaded', function() {
            var logoutLink = document.getElementById('logout-link');
            if (logoutLink) {
                logoutLink.addEventListener('click', function(e) {
                    if (!confirm('Apakah Anda yakin ingin logout?')) {
                        e.preventDefault();
                    }
                });
            }
        });
        </script>