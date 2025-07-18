<?php
session_start();
// Cek apakah pengguna sudah login, jika tidak, redirect ke halaman login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// File: public/barang_handler.php
// Skrip ini menangani logika untuk menambah, mengubah, dan menghapus data barang.

require_once '../config/database.php';

// AJAX: Cek apakah ada barang soft delete dengan nama & kategori sama
if (isset($_GET['cek_restore']) && $_GET['cek_restore'] == '1') {
    $nama = trim($_GET['nama_barang'] ?? '');
    $id_kat = $_GET['id_kategori'] ?? '';
    $result = ['restoreable' => false];
    if ($nama && $id_kat) {
        $stmt = $pdo->prepare("SELECT id_barang FROM tabel_barang WHERE nama_barang = ? AND id_kategori = ? AND is_deleted = 1 LIMIT 1");
        $stmt->execute([$nama, $id_kat]);
        $row = $stmt->fetch();
        if ($row) {
            $result = ['restoreable' => true, 'id_barang' => $row['id_barang']];
        }
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menangani penambahan barang baru ATAU restore barang soft delete
    if (isset($_POST['tambah_barang']) || !empty($_POST['restore_id_barang'])) {
        $nama_barang = trim($_POST['nama_barang']);
        $id_kategori = $_POST['id_kategori'];
        $harga_beli = $_POST['harga_beli'];
        $harga_jual = $_POST['harga_jual'];
        $stok = $_POST['stok'];

        // Validasi server-side
        if (empty($nama_barang) || empty($id_kategori) || !is_numeric($harga_beli) || !is_numeric($harga_jual) || !is_numeric($stok)) {
            $_SESSION['error'] = 'Semua field harus diisi dengan benar.';
            header('Location: tambah_barang.php');
            exit();
        }

        // Jika user memilih restore, lakukan restore
        if (!empty($_POST['restore_id_barang'])) {
            $restore_id = $_POST['restore_id_barang'];
            try {
                // Ambil stok lama sebelum restore
                $stmt_stok = $pdo->prepare("SELECT stok FROM tabel_barang WHERE id_barang =?");
                $stmt_stok->execute([$restore_id]);
                $stok_lama = $stmt_stok->fetchColumn();

                $sql = "UPDATE tabel_barang SET is_deleted = 0, nama_barang = ?, id_kategori = ?, harga_beli = ?, harga_jual = ?, stok = ? WHERE id_barang = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama_barang, $id_kategori, $harga_beli, $harga_jual, $stok, $restore_id]);

                // Log stok restore
                $log_sql = "INSERT INTO tabel_stock_log (id_barang, perubahan, stok_sebelum, stok_sesudah, keterangan, id_pengguna) VALUES (?,?,?,?,?,?)";
                $log_stmt = $pdo->prepare($log_sql);
                $log_stmt->execute([
                    $restore_id,
                    $stok - (int)$stok_lama,
                    (int)$stok_lama,
                    $stok,
                    'Restore barang soft delete',
                    $_SESSION['user_id']
                ]);

                $_SESSION['success'] = 'Barang berhasil direstore dan diperbarui.';
                header('Location: daftar_barang.php');
                exit();
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Gagal restore barang: '. $e->getMessage();
                header('Location: tambah_barang.php');
                exit();
            }
        }

        try {
            $sql = "INSERT INTO tabel_barang (nama_barang, id_kategori, harga_beli, harga_jual, stok) VALUES (?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama_barang, $id_kategori, $harga_beli, $harga_jual, $stok]);
            $id_barang_baru = $pdo->lastInsertId();

            // Log stok awal
            $log_sql = "INSERT INTO tabel_stock_log (id_barang, perubahan, stok_sebelum, stok_sesudah, keterangan, id_pengguna) VALUES (?,?,?,?,?,?)";
            $log_stmt = $pdo->prepare($log_sql);
            $log_stmt->execute([
                $id_barang_baru,
                $stok,
                0,
                $stok,
                'Stok awal saat tambah barang',
                $_SESSION['user_id']
            ]);

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
            // Ambil stok lama sebelum update
            $stok_lama = 0;
            $stmt_stok = $pdo->prepare("SELECT stok FROM tabel_barang WHERE id_barang =?");
            $stmt_stok->execute([$id_barang]);
            $stok_lama = $stmt_stok->fetchColumn();

            $sql = "UPDATE tabel_barang SET nama_barang =?, id_kategori =?, harga_beli =?, harga_jual =?, stok =? WHERE id_barang =?";
            $stmt = $pdo->prepare($sql); // [79, 82]
            $stmt->execute([$nama_barang, $id_kategori, $harga_beli, $harga_jual, $stok, $id_barang]);

            // Log perubahan stok jika berubah
            if ($stok != $stok_lama) {
                $perubahan = $stok - $stok_lama;
                $log_sql = "INSERT INTO tabel_stock_log (id_barang, perubahan, stok_sebelum, stok_sesudah, keterangan, id_pengguna) VALUES (?,?,?,?,?,?)";
                $log_stmt = $pdo->prepare($log_sql);
                $log_stmt->execute([
                    $id_barang,
                    $perubahan,
                    $stok_lama,
                    $stok,
                    'Edit stok barang',
                    $_SESSION['user_id']
                ]);
            }

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
        // Ambil stok sebelum hapus
        $stmt_stok = $pdo->prepare("SELECT stok FROM tabel_barang WHERE id_barang =? AND is_deleted = 0");
        $stmt_stok->execute([$id_barang]);
        $stok_lama = $stmt_stok->fetchColumn();

        // Log penghapusan stok (stok menjadi 0)
        if ($stok_lama !== false) {
            $log_sql = "INSERT INTO tabel_stock_log (id_barang, perubahan, stok_sebelum, stok_sesudah, keterangan, id_pengguna) VALUES (?,?,?,?,?,?)";
            $log_stmt = $pdo->prepare($log_sql);
            $log_stmt->execute([
                $id_barang,
                -$stok_lama,
                $stok_lama,
                0,
                'Barang dihapus (soft delete)',
                $_SESSION['user_id']
            ]);
        }

        // Soft delete barang
        $sql = "UPDATE tabel_barang SET is_deleted = 1 WHERE id_barang =?";
        $stmt = $pdo->prepare($sql); // [83]
        $stmt->execute([$id_barang]);

        $_SESSION['success'] = 'Barang berhasil dihapus (soft delete).';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus barang: '. $e->getMessage();
    }
    
    header('Location: daftar_barang.php');
    exit();
}
?>