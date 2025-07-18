<?php
require_once '../config/database.php';

// Query gabungan untuk laporan penjualan
$sql = "
SELECT 
    p.tanggal_penjualan AS tanggal,
    b.nama_barang,
    d.jumlah,
    (d.jumlah * d.harga_saat_transaksi) AS total
FROM tabel_penjualan p
JOIN tabel_detail_penjualan d ON p.id_penjualan = d.id_penjualan
JOIN tabel_barang b ON d.id_barang = b.id_barang
ORDER BY p.tanggal_penjualan DESC
";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
</head>
<body>
    <h2>Laporan Penjualan</h2>
    <a href="export_penjualan.php" target="_blank">Export ke Excel</a>
    <table border="1" cellpadding="8">
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Total</th>
        </tr>
        <?php
        $no = 1;
        foreach($rows as $row) {
            echo "<tr>";
            echo "<td>".$no++."</td>";
            echo "<td>".date('d-m-Y H:i:s', strtotime($row['tanggal']))."</td>";
            echo "<td>".htmlspecialchars($row['nama_barang'])."</td>";
            echo "<td>".$row['jumlah']."</td>";
            echo "<td>Rp ".number_format($row['total'], 2, ',', '.')."</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
