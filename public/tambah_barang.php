<?php
require_once '../config/database.php';
require_once 'templates/header.php';

// Ambil daftar kategori untuk dropdown
$kategori_stmt = $pdo->query("SELECT * FROM tabel_kategori ORDER BY nama_kategori");
$kategori_list = $kategori_stmt->fetchAll();
?>

<h2>Tambah Barang Baru</h2>
<form action="barang_handler.php" method="post">
    <div class="form-group">
        <label for="nama_barang">Nama Barang</label>
        <input type="text" id="nama_barang" name="nama_barang" required>
    </div>
    <div class="form-group">
        <label for="id_kategori">Kategori</label>
        <select id="id_kategori" name="id_kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategori_list as $kategori):?>
                <option value="<?php echo $kategori['id_kategori'];?>">
                    <?php echo htmlspecialchars($kategori['nama_kategori']);?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
    <div class="form-group">
        <label for="harga_beli">Harga Beli (Rp)</label>
        <input type="number" id="harga_beli" name="harga_beli" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="harga_jual">Harga Jual (Rp)</label>
        <input type="number" id="harga_jual" name="harga_jual" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="stok">Jumlah Stok</label>
        <input type="number" id="stok" name="stok" required>
    </div>
    <button type="submit" name="tambah_barang" class="btn">Tambah Barang</button>
    <a href="daftar_barang.php" class="btn btn-secondary">Batal</a>
</form>

<?php require_once 'templates/footer.php';?>