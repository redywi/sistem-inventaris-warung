<?php
session_start();
require_once '../config/database.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password tidak boleh kosong.';
        header('Location: login.php');
        exit();
    }

    try {
        $sql = "SELECT * FROM tabel_pengguna WHERE username =?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // pastikan fetch asosiatif

        // Debug: Uncomment baris berikut untuk melihat hash password dari database
        // if ($user) { echo "Hash di DB: " . $user['password']; exit(); }

        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil, simpan data pengguna ke session
            $_SESSION['user_id'] = $user['id_pengguna'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            header('Location: dashboard.php');
            exit();
        } else {
            // Debug: Uncomment baris berikut untuk cek password_verify
            // if ($user) { var_dump(password_verify($password, $user['password'])); exit(); }
            $_SESSION['error'] = 'Username atau password salah.';
            header('Location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan pada database.';
        header('Location: login.php');
        exit();
    }
} else {
    // Jika akses langsung, redirect ke halaman login
    header('Location: login.php');
    exit();
}
?>