<?php
session_start();
require_once 'config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
        $error = "Semua kolom wajib diisi!";
    } else {
        // Check duplicate
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username atau Email sudah terdaftar!";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (fullname, username, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $username, $email, $hashed_pass);

            if ($stmt->execute()) {
                $success = "Pendaftaran berhasil! Silakan <a href='login.php' style='color:#2563eb;'>Login</a>";
            } else {
                $error = "Terjadi kesalahan sistem, silakan coba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Futsal Zone</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">⚽ Futsal <span>Zone</span></a>
        <ul class="nav-links">
            <li><a href="index.php">Sewa Lapangan</a></li>
            <li><a href="login.php">Masuk</a></li>
            <li><a href="register.php" class="btn-auth">Daftar</a></li>
        </ul>
    </nav>

    <div class="auth-box">
        <h2>Daftar Akun Baru</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="fullname" required placeholder="Contoh: Budi Santoso">
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Contoh: budis123">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Contoh: budi@gmail.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="******">
            </div>
            <button type="submit" class="btn-book">Daftar Sekarang</button>
        </form>

        <p style="text-align: center; margin-top: 15px; font-size: 0.9rem; color: #64748b;">
            Sudah punya akun? <a href="login.php" style="color: #2563eb; font-weight: 600;">Masuk di sini</a>
        </p>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> Futsal Zone. All Rights Reserved.</p>
    </footer>
</body>
</html>
