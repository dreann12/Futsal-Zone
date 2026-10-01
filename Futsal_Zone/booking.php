<?php
session_start();
require_once 'config/db.php';

// Check if user logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$court_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM courts WHERE id = ?");
$stmt->bind_param("i", $court_id);
$stmt->execute();
$court = $stmt->get_result()->fetch_assoc();

if (!$court) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_date = $_POST['booking_date'];
    $start_time   = $_POST['start_time'];
    $duration     = intval($_POST['duration']);

    if (empty($booking_date) || empty($start_time) || $duration < 1) {
        $error = "Mohon lengkapi semua data booking!";
    } else {
        $total_price = $court['price_per_hour'] * $duration;
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("INSERT INTO bookings (user_id, court_id, booking_date, start_time, duration_hours, total_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissid", $user_id, $court_id, $booking_date, $start_time, $duration, $total_price);

        if ($stmt->execute()) {
            $success = "Booking berhasil disimpan! Silakan cek di menu Riwayat Booking.";
        } else {
            $error = "Gagal memproses booking. Coba beberapa saat lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Booking - Futsal Zone</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">⚽ Futsal <span>Zone</span></a>
        <ul class="nav-links">
            <li><a href="index.php">Sewa Lapangan</a></li>
            <li><a href="my_bookings.php">Riwayat Booking</a></li>
            <li style="color: #38bdf8; font-weight: 600;">Halo, <?= htmlspecialchars($_SESSION['fullname']) ?></li>
            <li><a href="logout.php" class="btn-logout">Logout</a></li>
        </ul>
    </nav>

    <div class="auth-box" style="max-width: 600px;">
        <h2>Form Booking Lapangan</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= $success ?><br><br>
                <a href="my_bookings.php" class="btn-book" style="display:inline-block; width:auto; padding:8px 16px;">Lihat Riwayat</a>
            </div>
        <?php else: ?>
            <div style="background: #f1f5f9; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <h3 style="color: #0f172a; margin-bottom: 5px;"><?= htmlspecialchars($court['name']) ?></h3>
                <p style="color: #2563eb; font-weight: 700; margin-bottom: 8px;">
                    Rp <?= number_format($court['price_per_hour'], 0, ',', '.') ?> / jam
                </p>
                <p style="font-size: 0.85rem; color: #475569;"><strong>Bola:</strong> <?= htmlspecialchars($court['ball_quality']) ?></p>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Tanggal Bermain</label>
                    <input type="date" name="booking_date" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label>Jam Mulai</label>
                    <select name="start_time" required>
                        <option value="">-- Pilih Jam --</option>
                        <option value="08:00:00">08:00 WIB</option>
                        <option value="09:00:00">09:00 WIB</option>
                        <option value="10:00:00">10:00 WIB</option>
                        <option value="13:00:00">13:00 WIB</option>
                        <option value="15:00:00">15:00 WIB</option>
                        <option value="17:00:00">17:00 WIB</option>
                        <option value="19:00:00">19:00 WIB</option>
                        <option value="20:00:00">20:00 WIB</option>
                        <option value="21:00:00">21:00 WIB</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Durasi Bermain (Jam)</label>
                    <input type="number" name="duration" min="1" max="5" value="1" required>
                </div>

                <button type="submit" class="btn-book">Konfirmasi & Simpan Booking</button>
            </form>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> Futsal Zone Indonesia. All Rights Reserved.</p>
    </footer>
</body>
</html>
