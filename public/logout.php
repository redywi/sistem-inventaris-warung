<?php
session_start();
// Hapus semua session
$_SESSION = [];
session_unset();
session_destroy();

// Cegah cache agar tidak bisa kembali ke halaman sebelumnya
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

// Redirect ke halaman login
header("Location: login.php");
exit();
?>