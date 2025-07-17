<?php
require_once '../config/database.php';
require_once 'templates/header.php';

// Ambil semua data barang dengan nama kategorinya
$stmt = $pdo->query("
    SELECT b.id_barang, b.nama_barang, k.nama_kategori, b.harga_beli, b.harga_jual, b.stok
    FROM tabel_barang b
    LEFT JOIN tabel_kategori k ON b.id_kategori = k.id_kategori
    ORDER BY b.nama_barang ASC
");
$barang_list = $stmt->fetchAll();
?>

<h2>Manajemen Daftar Barang</h2>
<a href="tambah_barang.php" class="btn">Tambah Barang Baru</a>
<br><br>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($barang_list) > 0):?>
            <?php foreach ($barang_list as $index => $barang):?>
                <tr>
                    <td><?php echo $index + 1;?></td>
                    <td><?php echo htmlspecialchars($barang['nama_barang']);?></td>
                    <td><?php echo htmlspecialchars($barang['nama_kategori']);?></td>
                    <td>Rp <?php echo number_format($barang['harga_beli'], 2, ',', '.');?></td>
                    <td>Rp <?php echo number_format($barang['harga_jual'], 2, ',', '.');?></td>
                    <td><?php echo $barang['stok'];?></td>
                    <td>
                        <a href="edit_barang.php?id=<?php echo $barang['id_barang'];?>" class="btn-edit">Edit</a>
                        <a href="barang_handler.php?hapus_id=<?php echo $barang['id_barang'];?>" class="btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endforeach;?>
        <?php else:?>
            <tr>
                <td colspan="7">Belum ada data barang.</td>
            </tr>
        <?php endif;?>
    </tbody>
</table>

<?php require_once 'templates/footer.php';?>