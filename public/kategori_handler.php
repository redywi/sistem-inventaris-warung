<?php
session_start();
// Cek apakah pengguna sudah login, jika tidak, redirect ke halaman login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
// File: public/kategori_handler.php
// Skrip ini menangani logika untuk menambah, mengubah, dan menghapus data kategori.

require_once '../config/database.php';

// Memeriksa metode request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menangani penambahan kategori baru
    if (isset($_POST['tambah_kategori'])) {
        $nama_kategori = trim($_POST['nama_kategori']);

        // Validasi server-side
        if (empty($nama_kategori)) {
            $_SESSION['error'] = 'Nama kategori tidak boleh kosong.';
            header('Location: tambah_kategori.php');
            exit();
        }

        try {
            $sql = "INSERT INTO tabel_kategori (nama_kategori) VALUES (?)";
            $stmt = $pdo->prepare($sql); // [76, 77]
            $stmt->execute([$nama_kategori]); // [78]

            $_SESSION['success'] = 'Kategori berhasil ditambahkan.';
            header('Location: daftar_kategori.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal menambahkan kategori: '. $e->getMessage();
            header('Location: tambah_kategori.php');
            exit();
        }
    }

    // Menangani pembaruan kategori
    if (isset($_POST['update_kategori'])) {
        $id_kategori = $_POST['id_kategori'];
        $nama_kategori = trim($_POST['nama_kategori']);

        if (empty($nama_kategori) || empty($id_kategori)) {
            $_SESSION['error'] = 'Data tidak lengkap.';
            header('Location: edit_kategori.php?id='. $id_kategori);
            exit();
        }

        try {
            $sql = "UPDATE tabel_kategori SET nama_kategori =? WHERE id_kategori =?";
            $stmt = $pdo->prepare($sql); // [77, 79]
            $stmt->execute([$nama_kategori, $id_kategori]);

            $_SESSION['success'] = 'Kategori berhasil diperbarui.';
            header('Location: daftar_kategori.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal memperbarui kategori: '. $e->getMessage();
            header('Location: edit_kategori.php?id='. $id_kategori);
            exit();
        }
    }
}

// Menangani penghapusan kategori (menggunakan GET untuk kesederhanaan, POST lebih aman untuk operasi state-changing)
if (isset($_GET['hapus_id'])) {
    $id_kategori = $_GET['hapus_id'];

    try {
        // Cek dulu apakah ada barang yang menggunakan kategori ini
        $check_sql = "SELECT COUNT(*) FROM tabel_barang WHERE id_kategori =? AND is_deleted = 0";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$id_kategori]);
        if ($check_stmt->fetchColumn() > 0) {
            $_SESSION['error'] = 'Tidak dapat menghapus kategori karena masih digunakan oleh beberapa barang.';
            header('Location: daftar_kategori.php');
            exit();
        }

        $sql = "DELETE FROM tabel_kategori WHERE id_kategori =?";
        $stmt = $pdo->prepare($sql); // [80]
        $stmt->execute([$id_kategori]);

        $_SESSION['success'] = 'Kategori berhasil dihapus.';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus kategori: '. $e->getMessage();
    }
    
    header('Location: daftar_kategori.php');
    exit();
}
?>