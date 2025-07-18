<?php
session_start();
require_once '../config/database.php';
require_once 'templates/header.php';

// --- Query untuk Kartu Statistik ---

// 1. Menghitung jumlah total jenis barang
$stmt_total_barang = $pdo->query("SELECT COUNT(*) FROM tabel_barang WHERE is_deleted = 0");
$total_barang = $stmt_total_barang->fetchColumn();

// 2. Menghitung total nilai inventaris (berdasarkan harga beli)
$stmt_nilai_inventaris = $pdo->query("SELECT SUM(harga_beli * stok) FROM tabel_barang WHERE is_deleted = 0");
$nilai_inventaris = $stmt_nilai_inventaris->fetchColumn();

// 3. Menghitung pendapatan hari ini
$stmt_pendapatan_hari_ini = $pdo->query("SELECT SUM(total_harga) FROM tabel_penjualan WHERE DATE(tanggal_penjualan) = CURDATE()");
$pendapatan_hari_ini = $stmt_pendapatan_hari_ini->fetchColumn();

// 4. Menghitung total transaksi hari ini
$stmt_transaksi_hari_ini = $pdo->query("SELECT COUNT(*) FROM tabel_penjualan WHERE DATE(tanggal_penjualan) = CURDATE()");
$transaksi_hari_ini = $stmt_transaksi_hari_ini->fetchColumn();

// --- Query untuk Daftar Stok Menipis ---
$low_stock_threshold = 5;
$stmt_low_stock = $pdo->prepare("SELECT nama_barang, stok FROM tabel_barang WHERE stok <=? AND is_deleted = 0 ORDER BY stok ASC");
$stmt_low_stock->execute([$low_stock_threshold]);
$low_stock_items = $stmt_low_stock->fetchAll();
?>

<h2>Dasbor Inventaris</h2>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Pendapatan Hari Ini</h3>
        <p>Rp <?php echo number_format($pendapatan_hari_ini?? 0, 2, ',', '.');?></p>
    </div>
    <div class="stat-card">
        <h3>Transaksi Hari Ini</h3>
        <p><?php echo $transaksi_hari_ini?? 0;?></p>
    </div>
    <div class="stat-card">
        <h3>Total Jenis Barang</h3>
        <p><?php echo $total_barang?? 0;?></p>
    </div>
    <div class="stat-card">
        <h3>Total Nilai Inventaris</h3>
        <p>Rp <?php echo number_format($nilai_inventaris?? 0, 2, ',', '.');?></p>
    </div>
</div>

<div class="dashboard-low-stock">
    <h3>Barang dengan Stok Menipis (Stok <= <?php echo $low_stock_threshold;?>)</h3>
    <?php if (count($low_stock_items) > 0):?>
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Sisa Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($low_stock_items as $item):?>
                    <tr class="low-stock-row">
                        <td><?php echo htmlspecialchars($item['nama_barang']);?></td>
                        <td><?php echo $item['stok'];?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    <?php else:?>
        <p>Tidak ada barang dengan stok menipis. Kerja bagus!</p>
    <?php endif;?>
</div>

<a href="stock_log.php" class="btn" style="margin-top:24px;">Riwayat Perubahan Stok</a>

<?php require_once 'templates/footer.php';?>