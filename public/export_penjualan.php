<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=laporan_penjualan.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('No', 'Tanggal', 'Nama Barang', 'Jumlah', 'Total'));

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

$no = 1;
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, array(
        $no++,
        date('d-m-Y H:i:s', strtotime($row['tanggal'])),
        $row['nama_barang'],
        $row['jumlah'],
        $row['total']
    ));
}
fclose($output);
exit;
