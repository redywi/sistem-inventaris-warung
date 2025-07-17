<?php
// File: public/barang_handler.php
// Skrip ini menangani logika untuk menambah, mengubah, dan menghapus data barang.

session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menangani penambahan barang baru
    if (isset($_POST['tambah_barang'])) {
        // Mengambil dan membersihkan data dari form
        $nama_barang = trim($_POST['nama_barang']);
        $id_kategori = $_POST['id_kategori'];
        $harga_beli = $_POST['harga_beli'];
        $harga_jual = $_POST['harga_jual'];
        $stok = $_POST['stok'];

        // Validasi server-side
        if (empty($nama_barang) || empty($id_kategori) ||!is_numeric($harga_beli) ||!is_numeric($harga_jual) ||!is_numeric($stok)) {
            $_SESSION['error'] = 'Semua field harus diisi dengan benar.';
            header('Location: tambah_barang.php');
            exit();
        }

        try {
            $sql = "INSERT INTO tabel_barang (nama_barang, id_kategori, harga_beli, harga_jual, stok) VALUES (?,?,?,?,?)";
            $stmt = $pdo->prepare($sql); // [78, 81]
            $stmt->execute([$nama_barang, $id_kategori, $harga_beli, $harga_jual, $stok]);

            $_SESSION['success'] = 'Barang berhasil ditambahkan.';
            header('Location: daftar_barang.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal menambahkan barang: '. $e->getMessage();
            header('Location: tambah_barang.php');
            exit();
        }
    }

    // Menangani pembaruan barang
    if (isset($_POST['update_barang'])) {
        $id_barang = $_POST['id_barang'];
        $nama_barang = trim($_POST['nama_barang']);
        $id_kategori = $_POST['id_kategori'];
        $harga_beli = $_POST['harga_beli'];
        $harga_jual = $_POST['harga_jual'];
        $stok = $_POST['stok'];

        if (empty($id_barang) || empty($nama_barang) || empty($id_kategori) ||!is_numeric($harga_beli) ||!is_numeric($harga_jual) ||!is_numeric($stok)) {
            $_SESSION['error'] = 'Semua field harus diisi dengan benar.';
            header('Location: edit_barang.php?id='. $id_barang);
            exit();
        }

        try {
            $sql = "UPDATE tabel_barang SET nama_barang =?, id_kategori =?, harga_beli =?, harga_jual =?, stok =? WHERE id_barang =?";
            $stmt = $pdo->prepare($sql); // [79, 82]
            $stmt->execute([$nama_barang, $id_kategori, $harga_beli, $harga_jual, $stok, $id_barang]);

            $_SESSION['success'] = 'Barang berhasil diperbarui.';
            header('Location: daftar_barang.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal memperbarui barang: '. $e->getMessage();
            header('Location: edit_barang.php?id='. $id_barang);
            exit();
        }
    }
}

// Menangani penghapusan barang
if (isset($_GET['hapus_id'])) {
    $id_barang = $_GET['hapus_id'];

    try {
        $sql = "DELETE FROM tabel_barang WHERE id_barang =?";
        $stmt = $pdo->prepare($sql); // [83]
        $stmt->execute([$id_barang]);

        $_SESSION['success'] = 'Barang berhasil dihapus.';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus barang: '. $e->getMessage();
    }
    
    header('Location: daftar_barang.php');
    exit();
}
?>