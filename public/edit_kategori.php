<?php
session_start();
// Cek apakah pengguna sudah login, jika tidak, redirect ke halaman login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
// File: public/edit_kategori.php
// Halaman untuk mengedit data kategori yang sudah ada.

// Memuat file konfigurasi database dan template header.
require_once '../config/database.php';
require_once 'templates/header.php';

// Inisialisasi variabel.
$kategori = null;
$id_kategori = null;

// Memeriksa apakah 'id' ada di URL dan merupakan angka.
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_kategori = $_GET['id'];

    try {
        // Menyiapkan dan menjalankan query untuk mengambil data kategori berdasarkan ID.
        $stmt = $pdo->prepare("SELECT * FROM tabel_kategori WHERE id_kategori =?"); // [19, 20]
        $stmt->execute([$id_kategori]);
        $kategori = $stmt->fetch(PDO::FETCH_ASSOC); // Mengambil data. [5]

        // Jika kategori dengan ID tersebut tidak ditemukan, tampilkan pesan.
        if (!$kategori) {
            echo "<div class='alert alert-error'>Kategori tidak ditemukan.</div>";
            $id_kategori = null; // Set ID menjadi null agar form tidak ditampilkan.
        }
    } catch (PDOException $e) {
        // Menangani error jika query gagal.
        echo "<div class='alert alert-error'>Gagal mengambil data: ". $e->getMessage(). "</div>";
        $id_kategori = null;
    }
} else {
    echo "<div class='alert alert-error'>ID kategori tidak valid.</div>";
}

// Hanya tampilkan form jika data kategori berhasil ditemukan.
if ($kategori && $id_kategori):
?>

<h2>Edit Kategori</h2>
<form action="kategori_handler.php" method="post">
    <input type="hidden" name="id_kategori" value="<?php echo $kategori['id_kategori'];?>">

    <div class="form-group">
        <label for="nama_kategori">Nama Kategori</label>
        <input type="text" id="nama_kategori" name="nama_kategori" value="<?php echo htmlspecialchars($kategori['nama_kategori']);?>" required>
    </div>

    <button type="submit" name="update_kategori" class="btn">Perbarui Kategori</button>
    <a href="daftar_kategori.php" class="btn btn-secondary">Batal</a>
</form>

<?php
endif; // Akhir dari blok if ($kategori && $id_kategori)

// Memuat template footer.
require_once 'templates/footer.php';
?>