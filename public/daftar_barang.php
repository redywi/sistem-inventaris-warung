<?php
session_start();
// Cek apakah pengguna sudah login, jika tidak, redirect ke halaman login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
require_once 'templates/header.php';

// Ambil semua data barang dengan nama kategorinya
$stmt = $pdo->query("
    SELECT b.id_barang, b.nama_barang, k.nama_kategori, b.harga_beli, b.harga_jual, b.stok
    FROM tabel_barang b
    LEFT JOIN tabel_kategori k ON b.id_kategori = k.id_kategori
    WHERE b.is_deleted = 0
    ORDER BY b.nama_barang ASC
");
$barang_list = $stmt->fetchAll();

// Ambil daftar kategori untuk filter
$kategori_stmt = $pdo->query("SELECT * FROM tabel_kategori ORDER BY nama_kategori");
$kategori_list = $kategori_stmt->fetchAll(PDO::FETCH_ASSOC);

// Proses pencarian dan filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_kategori = isset($_GET['filter_kategori']) ? $_GET['filter_kategori'] : '';

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "b.nama_barang LIKE ?";
    $params[] = "%$search%";
}
if ($filter_kategori !== '' && is_numeric($filter_kategori)) {
    $where[] = "b.id_kategori = ?";
    $params[] = $filter_kategori;
}
$where[] = "b.is_deleted = 0";

$sql = "
    SELECT b.id_barang, b.nama_barang, k.nama_kategori, b.harga_beli, b.harga_jual, b.stok
    FROM tabel_barang b
    LEFT JOIN tabel_kategori k ON b.id_kategori = k.id_kategori
";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY b.nama_barang ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$barang_list = $stmt->fetchAll();
?>

<h2>Manajemen Daftar Barang</h2>

<form method="get" style="margin-bottom:18px; display:flex; gap:10px; flex-wrap:wrap;">
    <input type="text" name="search" placeholder="Cari nama barang..." value="<?php echo htmlspecialchars($search); ?>" style="flex:1; min-width:180px;">
    <select name="filter_kategori" style="min-width:160px;">
        <option value="">-- Semua Kategori --</option>
        <?php foreach ($kategori_list as $kategori): ?>
            <option value="<?php echo $kategori['id_kategori']; ?>" <?php if ($filter_kategori == $kategori['id_kategori']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn">Cari/Filter</button>
    <a href="daftar_barang.php" class="btn btn-secondary">Reset</a>
</form>

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