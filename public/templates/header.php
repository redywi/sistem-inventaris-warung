<?php session_start();?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIW - Sistem Inventaris Warung</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistem Inventaris Warung (SIW)</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="daftar_barang.php">Manajemen Barang</a>
                <a href="daftar_kategori.php">Manajemen Kategori</a>
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