<?php
session_start();
// Cek apakah pengguna sudah login, jika tidak, redirect ke halaman login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';
require_once 'templates/header.php';

// Ambil daftar kategori untuk dropdown
$kategori_stmt = $pdo->query("SELECT * FROM tabel_kategori ORDER BY nama_kategori");
$kategori_list = $kategori_stmt->fetchAll();
?>

<h2>Tambah Barang Baru</h2>
<form action="barang_handler.php" method="post" id="form-tambah-barang">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-tambah-barang');
    form.addEventListener('submit', function(e) {
        const nama = document.getElementById('nama_barang').value.trim();
        const id_kat = document.getElementById('id_kategori').value;
        if (!nama || !id_kat) return; // biar validasi server tetap jalan

        e.preventDefault();
        fetch('barang_handler.php?cek_restore=1&nama_barang=' + encodeURIComponent(nama) + '&id_kategori=' + encodeURIComponent(id_kat))
            .then(res => res.json())
            .then(data => {
                if (data && data.restoreable) {
                    if (confirm('Barang dengan nama dan kategori ini pernah dihapus. Apakah Anda ingin mengembalikannya?')) {
                        // Submit dengan parameter restore
                        const inputRestore = document.createElement('input');
                        inputRestore.type = 'hidden';
                        inputRestore.name = 'restore_id_barang';
                        inputRestore.value = data.id_barang;
                        form.appendChild(inputRestore);

                        // Pastikan tombol submit bernama tambah_barang tetap ada
                        // (tidak perlu diubah, sudah ada di HTML)
                        form.submit();
                        return;
                    }
                }
                form.submit();
            })
            .catch(() => form.submit());
    });
});
</script>
<?php require_once 'templates/footer.php';?>