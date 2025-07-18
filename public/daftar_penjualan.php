<?php
session_start();
require_once '../config/database.php';
require_once 'templates/header.php';

// Proses filter
$search_kasir = isset($_GET['search_kasir']) ? trim($_GET['search_kasir']) : '';
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : '';

$where = [];
$params = [];

if ($search_kasir !== '') {
    $where[] = "u.nama_lengkap LIKE ?";
    $params[] = "%$search_kasir%";
}
if ($tgl_mulai !== '') {
    $where[] = "DATE(p.tanggal_penjualan) >= ?";
    $params[] = $tgl_mulai;
}
if ($tgl_selesai !== '') {
    $where[] = "DATE(p.tanggal_penjualan) <= ?";
    $params[] = $tgl_selesai;
}

$sql = "
    SELECT p.id_penjualan, p.tanggal_penjualan, p.total_harga, u.nama_lengkap
    FROM tabel_penjualan p
    JOIN tabel_pengguna u ON p.id_pengguna = u.id_pengguna
";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY p.tanggal_penjualan DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$penjualan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Riwayat Transaksi Penjualan</h2>

<a href="laporan_penjualan.php" target="_blank" class="btn" style="margin-bottom:18px;">Lihat Laporan Penjualan</a>

<form method="get" style="margin-bottom:18px; display:flex; gap:10px; flex-wrap:wrap;">
    <input type="text" name="search_kasir" placeholder="Cari nama kasir..." value="<?php echo htmlspecialchars($search_kasir); ?>" style="min-width:160px;">
    <input type="date" name="tgl_mulai" value="<?php echo htmlspecialchars($tgl_mulai); ?>">
    <input type="date" name="tgl_selesai" value="<?php echo htmlspecialchars($tgl_selesai); ?>">
    <button type="submit" class="btn">Cari/Filter</button>
    <a href="daftar_penjualan.php" class="btn btn-secondary">Reset</a>
</form>

<table>
    <thead>
        <tr>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Total Harga</th>
            <th>Kasir</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($penjualan_list) > 0):?>
            <?php foreach ($penjualan_list as $penjualan):?>
                <tr>
                    <td><?php echo $penjualan['id_penjualan'];?></td>
                    <td><?php echo date('d-m-Y H:i:s', strtotime($penjualan['tanggal_penjualan']));?></td>
                    <td>Rp <?php echo number_format($penjualan['total_harga'], 2, ',', '.');?></td>
                    <td><?php echo htmlspecialchars($penjualan['nama_lengkap']);?></td>
                </tr>
            <?php endforeach;?>
        <?php else:?>
            <tr>
                <td colspan="4">Belum ada riwayat penjualan.</td>
            </tr>
        <?php endif;?>
    </tbody>
</table>

<?php require_once 'templates/footer.php';?>