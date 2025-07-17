<?php
// File: public/tambah_kategori.php
// Halaman dengan formulir untuk menambah kategori baru.

// Memuat template header.
require_once 'templates/header.php';
?>

<h2>Tambah Kategori Baru</h2>
<form action="kategori_handler.php" method="post">
    <div class="form-group">
        <label for="nama_kategori">Nama Kategori</label>
        <input type="text" id="nama_kategori" name="nama_kategori" placeholder="Contoh: Minuman Dingin" required>
    </div>
    <button type="submit" name="tambah_kategori" class="btn">Tambah Kategori</button>
    <a href="daftar_kategori.php" class="btn btn-secondary">Batal</a>
</form>

<?php
// Memuat template footer.
require_once 'templates/footer.php';
?>