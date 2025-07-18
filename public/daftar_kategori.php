<?php
session_start();
// Cek apakah pengguna sudah login, jika tidak, redirect ke halaman login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// File: public/daftar_kategori.php
// Halaman untuk menampilkan daftar semua kategori barang.

// Memuat file konfigurasi database dan template header.
require_once '../config/database.php';
require_once 'templates/header.php';

// Proses pencarian kategori
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];
if ($search !== '') {
    $where = "WHERE nama_kategori LIKE ?";
    $params[] = "%$search%";
}

try {
    $stmt = $pdo->prepare("SELECT * FROM tabel_kategori $where ORDER BY nama_kategori ASC");
    $stmt->execute($params);
    $kategori_list = $stmt->fetchAll(PDO::FETCH_ASSOC); // [1, 2]
} catch (PDOException $e) {
    // Menangani error jika query gagal.
    echo "<div class='alert alert-error'>Gagal mengambil data kategori: ". $e->getMessage(). "</div>";
    $kategori_list =[]; // Set ke array kosong jika gagal.
}
?>

<h2>Manajemen Kategori Barang</h2>

<form method="get" style="margin-bottom:18px; display:flex; gap:10px; flex-wrap:wrap;">
    <input type="text" name="search" placeholder="Cari nama kategori..." value="<?php echo htmlspecialchars($search); ?>" style="flex:1; min-width:180px;">
    <button type="submit" class="btn">Cari</button>
    <a href="daftar_kategori.php" class="btn btn-secondary">Reset</a>
</form>

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