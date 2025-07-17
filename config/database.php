<?php
// File: config/database.php
// File ini bertanggung jawab untuk membuat dan mengonfigurasi koneksi ke database menggunakan PDO.

$host = 'localhost';
$db_name = 'db_siw';
$username = 'root'; // Nama pengguna default untuk XAMPP
$password = '';     // Kata sandi default untuk XAMPP

// DSN (Data Source Name) untuk koneksi MySQL
$dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";

// Opsi untuk koneksi PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Mengaktifkan mode error untuk menangani kesalahan dengan pengecualian
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Mengatur mode pengambilan data default ke associative array
    PDO::ATTR_EMULATE_PREPARES => false, // Nonaktifkan emulasi prepared statements untuk keamanan yang lebih baik
];

try {
    // Membuat instance PDO baru untuk koneksi database.
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // Jika koneksi gagal, hentikan eksekusi dan tampilkan pesan error.
    // Dalam aplikasi produksi, error ini harus dicatat (log) dan pesan yang lebih umum ditampilkan kepada pengguna.
    http_response_code(500);
    exit('Koneksi Database Gagal: '. $e->getMessage());
}

// Variabel $pdo sekarang siap digunakan oleh skrip lain yang menyertakan file ini.
?>