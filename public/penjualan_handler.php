<?php
session_start();
require_once '../config/database.php';

if (isset($_POST['simpan_penjualan']) && isset($_POST['barang'])) {
    // Debug: cek data POST yang diterima
    // echo '<pre>'; print_r($_POST); exit();

    $barang_keranjang = $_POST['barang'];
    $id_pengguna = $_SESSION['user_id'];
    $total_harga = 0;

    // Validasi data POST
    foreach ($barang_keranjang as $item) {
        if (
            !isset($item['id']) || !is_numeric($item['id']) ||
            !isset($item['jumlah']) || !is_numeric($item['jumlah'])
        ) {
            $_SESSION['error'] = 'Kolom id dan jumlah harus diisi dengan angka.';
            header('Location: tambah_penjualan.php');
            exit();
        }
    }

    // Mulai transaksi
    $pdo->beginTransaction();

    try {
        // 1. Hitung total harga dan validasi stok
        foreach ($barang_keranjang as $item) {
            $id_barang = $item['id'];
            $jumlah = (int)$item['jumlah'];

            $stmt = $pdo->prepare("SELECT nama_barang, harga_jual, stok FROM tabel_barang WHERE id_barang =? AND is_deleted = 0");
            $stmt->execute([$id_barang]);
            $barang_db = $stmt->fetch();

            if (!$barang_db || $jumlah > $barang_db['stok']) {
                throw new Exception("Stok untuk ". ($barang_db['nama_barang']?? 'barang tidak dikenal'). " tidak mencukupi.");
            }
            $total_harga += $barang_db['harga_jual'] * $jumlah;
        }

        // 2. Masukkan ke tabel_penjualan
        $sql_penjualan = "INSERT INTO tabel_penjualan (total_harga, id_pengguna) VALUES (?,?)";
        $stmt_penjualan = $pdo->prepare($sql_penjualan);
        $stmt_penjualan->execute([$total_harga, $id_pengguna]);
        $id_penjualan = $pdo->lastInsertId();

        // 3. Masukkan ke tabel_detail_penjualan dan update stok
        $sql_detail = "INSERT INTO tabel_detail_penjualan (id_penjualan, id_barang, jumlah, harga_saat_transaksi) VALUES (?,?,?,?)";
        $stmt_detail = $pdo->prepare($sql_detail);

        $sql_update_stok = "UPDATE tabel_barang SET stok = stok -? WHERE id_barang =?";
        $stmt_update_stok = $pdo->prepare($sql_update_stok);

        foreach ($barang_keranjang as $item) {
            $id_barang = $item['id'];
            $jumlah = (int)$item['jumlah'];

            $stmt_harga = $pdo->prepare("SELECT harga_jual FROM tabel_barang WHERE id_barang =? AND is_deleted = 0");
            $stmt_harga->execute([$id_barang]);
            $harga_saat_transaksi = $stmt_harga->fetchColumn();

            // Masukkan detail
            $stmt_detail->execute([$id_penjualan, $id_barang, $jumlah, $harga_saat_transaksi]);

            // Update stok
            $stmt_update_stok->execute([$jumlah, $id_barang]);

            // Log perubahan stok
            $stmt_stok = $pdo->prepare("SELECT stok FROM tabel_barang WHERE id_barang =? AND is_deleted = 0");
            $stmt_stok->execute([$id_barang]);
            $stok_sesudah = $stmt_stok->fetchColumn();
            $stok_sebelum = $stok_sesudah + $jumlah;

            $log_sql = "INSERT INTO tabel_stock_log (id_barang, perubahan, stok_sebelum, stok_sesudah, keterangan, id_pengguna) VALUES (?,?,?,?,?,?)";
            $log_stmt = $pdo->prepare($log_sql);
            $log_stmt->execute([
                $id_barang,
                -$jumlah,
                $stok_sebelum,
                $stok_sesudah,
                'Penjualan (ID Penjualan: '.$id_penjualan.')',
                $id_pengguna
            ]);
        }

        // Jika semua berhasil, commit transaksi
        $pdo->commit();
        $_SESSION['success'] = 'Transaksi berhasil disimpan.';
        header('Location: daftar_penjualan.php');
        exit();

    } catch (Exception $e) {
        // Jika ada kesalahan, rollback transaksi
        $pdo->rollBack();
        $_SESSION['error'] = 'Gagal menyimpan transaksi: '. $e->getMessage();
        header('Location: tambah_penjualan.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'Tidak ada barang di keranjang.';
    header('Location: tambah_penjualan.php');
    exit();
}
?>