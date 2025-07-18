<?php
session_start();
// Cek apakah pengguna sudah login, jika tidak, redirect ke halaman login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
// File: public/edit_barang.php
// Halaman untuk mengedit data barang yang sudah ada.

// Memulai session dan memuat file konfigurasi database dan header.
require_once '../config/database.php';
require_once 'templates/header.php';

// Inisialisasi variabel untuk menampung data barang dan daftar kategori.
$barang = null;
$kategori_list = [];
$id_barang = null;

// 1. Mengambil ID Barang dari URL dan Validasi
// Memeriksa apakah 'id' ada di URL dan merupakan angka. [1, 2]
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_barang = $_GET['id'];

    try {
        // 2. Mengambil Data Barang Spesifik dari Database
        // Menyiapkan dan menjalankan query untuk mengambil data barang berdasarkan ID.
        // Menggunakan prepared statement untuk keamanan.
        $stmt = $pdo->prepare("SELECT * FROM tabel_barang WHERE id_barang =? AND is_deleted = 0");
        $stmt->execute([$id_barang]);
        $barang = $stmt->fetch(PDO::FETCH_ASSOC); // Mengambil data sebagai associative array. [1]

        // Jika barang dengan ID tersebut tidak ditemukan, tampilkan pesan.
        if (!$barang) {
            echo "<div class='alert alert-error'>Barang tidak ditemukan.</div>";
            $id_barang = null; // Set ID menjadi null agar form tidak ditampilkan.
        }

        // 3. Mengambil Semua Data Kategori untuk Dropdown
        // Query ini mengambil semua kategori untuk ditampilkan di elemen <select>. [3, 4]
        $kategori_stmt = $pdo->query("SELECT * FROM tabel_kategori ORDER BY nama_kategori");
        $kategori_list = $kategori_stmt->fetchAll(PDO::FETCH_ASSOC); // [5]

    } catch (PDOException $e) {
        // Menangani error jika query gagal.
        echo "<div class='alert alert-error'>Gagal mengambil data: ". $e->getMessage(). "</div>";
        $id_barang = null;
    }
} else {
    echo "<div class='alert alert-error'>ID barang tidak valid.</div>";
}

// Hanya tampilkan form jika data barang berhasil ditemukan.
if ($barang && $id_barang):
?>

<h2>Edit Data Barang</h2>
<form action="barang_handler.php" method="post">
    <input type="hidden" name="id_barang" value="<?php echo htmlspecialchars($barang['id_barang']); ?>">

    <div class="form-group">
        <label for="nama_barang">Nama Barang</label>
        <input type="text" id="nama_barang" name="nama_barang" value="<?php echo htmlspecialchars($barang['nama_barang']);?>" required>
    </div>

    <div class="form-group">
        <label for="id_kategori">Kategori</label>
        <select id="id_kategori" name="id_kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategori_list as $kategori):?>
                <?php
                // Logika untuk menentukan kategori mana yang harus dipilih (selected)
                // Membandingkan id_kategori dari setiap item di loop dengan id_kategori dari barang yang sedang diedit.
                $selected = ($kategori['id_kategori'] == $barang['id_kategori'])? 'selected' : '';
               ?>
                <option value="<?php echo $kategori['id_kategori'];?>" <?php echo $selected;?>>
                    <?php echo htmlspecialchars($kategori['nama_kategori']);?>
                </option>
            <?php endforeach;?>
        </select>
    </div>

    <div class="form-group">
        <label for="harga_beli">Harga Beli (Rp)</label>
        <input type="number" id="harga_beli" name="harga_beli" step="0.01" value="<?php echo $barang['harga_beli'];?>" required>
    </div>

    <div class="form-group">
        <label for="harga_jual">Harga Jual (Rp)</label>
        <input type="number" id="harga_jual" name="harga_jual" step="0.01" value="<?php echo $barang['harga_jual'];?>" required>
    </div>

    <div class="form-group">
        <label for="stok">Jumlah Stok</label>
        <input type="number" id="stok" name="stok" value="<?php echo $barang['stok'];?>" required>
    </div>

    <button type="submit" name="update_barang" class="btn">Perbarui Barang</button>
    <a href="daftar_barang.php" class="btn btn-secondary">Batal</a>
</form>

<?php
endif; // Akhir dari blok if ($barang && $id_barang)

// Memuat file footer.
require_once 'templates/footer.php';
?>