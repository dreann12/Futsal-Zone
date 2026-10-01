<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT b.*, c.name AS court_name, c.price_per_hour 
        FROM bookings b 
        JOIN courts c ON b.court_id = c.id 
        WHERE b.user_id = ? 
        ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - Futsal Zone</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">⚽ Futsal <span>Zone</span></a>
        <ul class="nav-links">
            <li><a href="index.php">Sewa Lapangan</a></li>
            <li><a href="my_bookings.php" style="color: #38bdf8;">Riwayat Booking</a></li>
            <li style="color: #38bdf8; font-weight: 600;">Halo, <?= htmlspecialchars($_SESSION['fullname']) ?></li>
            <li><a href="logout.php" class="btn-logout">Logout</a></li>
        </ul>
    </nav>

    <div class="container" style="margin-top: 40px;">
        <h2 style="color: white; margin-bottom: 20px;">Riwayat Booking Saya</h2>

        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lapangan</th>
                            <th>Tanggal Bermain</th>
                            <th>Jam Mulai</th>
                            <th>Durasi</th>
                            <th>Total Biaya</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($row = $result->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['court_name']) ?></strong></td>
                                <td><?= date('d-m-Y', strtotime($row['booking_date'])) ?></td>
                                <td><?= substr($row['start_time'], 0, 5) ?> WIB</td>
                                <td><?= $row['duration_hours'] ?> Jam</td>
                                <td><strong style="color:#2563eb;">Rp <?= number_format($row['total_price'], 0, ',', '.') ?></strong></td>
                                <td>
                                    <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 700;">
                                        <?= strtoupper($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #64748b; padding: 20px;">Belum ada riwayat booking. <a href="index.php" style="color: #2563eb;">Sewa lapangan sekarang</a></p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> Futsal Zone Indonesia. All Rights Reserved.</p>
    </footer>
</body>
</html>
