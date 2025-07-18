<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';
require_once 'templates/header.php';

// Filter
$search_barang = isset($_GET['search_barang']) ? trim($_GET['search_barang']) : '';
$where = '';
$params = [];
if ($search_barang !== '') {
    $where = "WHERE b.nama_barang LIKE ?";
    $params[] = "%$search_barang%";
}

$sql = "
SELECT l.*, b.nama_barang, u.nama_lengkap
FROM tabel_stock_log l
LEFT JOIN tabel_barang b ON l.id_barang = b.id_barang
LEFT JOIN tabel_pengguna u ON l.id_pengguna = u.id_pengguna
$where
ORDER BY l.tanggal_log DESC
LIMIT 200
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Riwayat Perubahan Stok Barang</h2>

<form method="get" style="margin-bottom:18px; display:flex; gap:10px; flex-wrap:wrap;">
    <input type="text" name="search_barang" placeholder="Cari nama barang..." value="<?php echo htmlspecialchars($search_barang); ?>" style="flex:1; min-width:180px;">
    <button type="submit" class="btn">Cari</button>
    <a href="stock_log.php" class="btn btn-secondary">Reset</a>
</form>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Perubahan</th>
            <th>Stok Sebelum</th>
            <th>Stok Sesudah</th>
            <th>Keterangan</th>
            <th>Oleh</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($logs) > 0):?>
            <?php foreach ($logs as $i => $log):?>
                <tr>
                    <td><?php echo $i+1;?></td>
                    <td><?php echo date('d-m-Y H:i:s', strtotime($log['tanggal_log']));?></td>
                    <td><?php echo htmlspecialchars($log['nama_barang'] ?? '[Barang dihapus]');?></td>
                    <td><?php echo ($log['perubahan'] > 0 ? '+' : '') . $log['perubahan'];?></td>
                    <td><?php echo $log['stok_sebelum'];?></td>
                    <td><?php echo $log['stok_sesudah'];?></td>
                    <td><?php echo htmlspecialchars($log['keterangan']);?></td>
                    <td><?php echo htmlspecialchars($log['nama_lengkap'] ?? '-');?></td>
                </tr>
            <?php endforeach;?>
        <?php else:?>
            <tr>
                <td colspan="8">Belum ada riwayat perubahan stok.</td>
            </tr>
        <?php endif;?>
    </tbody>
</table>

<?php require_once 'templates/footer.php';?>
