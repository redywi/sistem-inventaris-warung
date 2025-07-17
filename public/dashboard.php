<?php
require_once '../config/database.php';
require_once 'templates/header.php';

// --- Query untuk Kartu Statistik ---

// 1. Menghitung jumlah total jenis barang
$stmt_total_barang = $pdo->query("SELECT COUNT(*) FROM tabel_barang");
$total_barang = $stmt_total_barang->fetchColumn();

// 2. Menghitung jumlah total kategori
$stmt_total_kategori = $pdo->query("SELECT COUNT(*) FROM tabel_kategori");
$total_kategori = $stmt_total_kategori->fetchColumn();

// 3. Menghitung total nilai inventaris (berdasarkan harga beli)
$stmt_nilai_inventaris = $pdo->query("SELECT SUM(harga_beli * stok) FROM tabel_barang");
$nilai_inventaris = $stmt_nilai_inventaris->fetchColumn();

// --- Query untuk Daftar Stok Menipis ---
$low_stock_threshold = 5;
$stmt_low_stock = $pdo->prepare("SELECT nama_barang, stok FROM tabel_barang WHERE stok <=? ORDER BY stok ASC");
$stmt_low_stock->execute([$low_stock_threshold]);
$low_stock_items = $stmt_low_stock->fetchAll();
?>

<h2>Dasbor Inventaris</h2>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Total Jenis Barang</h3>
        <p><?php echo $total_barang;?></p>
    </div>
    <div class="stat-card">
        <h3>Total Kategori</h3>
        <p><?php echo $total_kategori;?></p>
    </div>
    <div class="stat-card">
        <h3>Total Nilai Inventaris</h3>
        <p>Rp <?php echo number_format($nilai_inventaris, 2, ',', '.');?></p>
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

<?php require_once 'templates/footer.php';?>