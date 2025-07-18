<?php
session_start();
// Jika sudah login, redirect ke dashboard
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIW</title>
    <link rel="stylesheet" href="/sistem-inventaris-warung/public/style.css">
</head>
<body class="login-page">
    <div class="container">
        <h2 class="login-title">Login Sistem Inventaris Warung</h2>
        <form action="auth_handler.php" method="post">
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-error">'. htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">'. htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
        </form>
    </div>
</body>
</html>