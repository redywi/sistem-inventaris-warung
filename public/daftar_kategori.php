<?php
// File: public/daftar_kategori.php
// Halaman untuk menampilkan daftar semua kategori barang.

// Memuat file konfigurasi database dan template header.
require_once '../config/database.php';
require_once 'templates/header.php';

// Mengambil semua data dari tabel kategori, diurutkan berdasarkan nama.
try {
    $stmt = $pdo->query("SELECT * FROM tabel_kategori ORDER BY nama_kategori ASC");
    $kategori_list = $stmt->fetchAll(PDO::FETCH_ASSOC); // [1, 2]
} catch (PDOException $e) {
    // Menangani error jika query gagal.
    echo "<div class='alert alert-error'>Gagal mengambil data kategori: ". $e->getMessage(). "</div>";
    $kategori_list =[]; // Set ke array kosong jika gagal.
}
?>

<h2>Manajemen Kategori Barang</h2>
<a href="tambah_kategori.php" class="btn">Tambah Kategori Baru</a>
<br><br>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kategori</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($kategori_list) > 0):?>
            <?php foreach ($kategori_list as $index => $kategori):?>
                <tr>
                    <td><?php echo $index + 1;?></td>
                    <td><?php echo htmlspecialchars($kategori['nama_kategori']); ?></td>
                    <td>
                        <a href="edit_kategori.php?id=<?php echo $kategori['id_kategori'];?>" class="btn-edit">Edit</a>
                        <a href="kategori_handler.php?hapus_id=<?php echo $kategori['id_kategori'];?>" class="btn-hapus" onclick="return confirm('Menghapus kategori ini akan gagal jika masih ada barang yang terkait. Lanjutkan?');">Hapus</a> 
                    </td>
                </tr>
            <?php endforeach;?>
        <?php else:?>
            <tr>
                <td colspan="3">Belum ada data kategori.</td>
            </tr>
        <?php endif;?>
    </tbody>
</table>

<?php
// Memuat template footer.
require_once 'templates/footer.php';
?>