<?php
session_start();
require_once 'config/db.php';

// Proteksi Halaman: Hanya Admin yang boleh masuk
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fitur Aksi Admin (Konfirmasi / Batalkan / Hapus Booking)
if (isset($_GET['action']) && isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
    $action = $_GET['action'];

    if ($action === 'confirm') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
    } elseif ($action === 'cancel') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Statistik Pengelola / Admin
$total_bookings = $conn->query("SELECT COUNT(*) as total FROM bookings")->fetch_assoc()['total'];
$total_income   = $conn->query("SELECT SUM(total_price) as total FROM bookings WHERE status = 'confirmed'")->fetch_assoc()['total'] ?? 0;
$total_users    = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];
$total_courts   = $conn->query("SELECT COUNT(*) as total FROM courts")->fetch_assoc()['total'];

// Ambil Seluruh Data Booking Masuk
$sql_bookings = "SELECT b.*, u.fullname, u.email, c.name as court_name 
                 FROM bookings b 
                 JOIN users u ON b.user_id = u.id 
                 JOIN courts c ON b.court_id = c.id 
                 ORDER BY b.created_at DESC";
$all_bookings = $conn->query($sql_bookings);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Futsal Zone</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .btn-action {
            padding: 4px 8px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Navbar Admin -->
    <nav class="navbar">
        <a href="dashboard.php" class="logo">⚽ Futsal <span>Zone</span></a>
        <ul class="nav-links">
            <li><a href="index.php">Lihat Website</a></li>
            <li><a href="admin_dashboard.php" style="color: #f59e0b; font-weight:700;">Dashboard Admin</a></li>
            <li style="color: #38bdf8; font-weight: 600;">Admin: <?= htmlspecialchars($_SESSION['fullname']) ?></li>
            <li><a href="logout.php" class="btn-logout">Logout</a></li>
        </ul>
    </nav>

    <div class="container" style="margin-top: 30px;">
        <div class="admin-header">
            <h1 style="margin:0 0 10px 0;">Panel Kelola Admin Futsal Zone ⚙️</h1>
            <p style="margin:0; opacity: 0.8;">Kelola seluruh reservasi pelanggan, status pembayaran, dan statistik keuangan.</p>
        </div>

        <!-- 4 Grid Kartu Ringkasan Admin -->
        <div class="court-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 30px;">
            <div class="court-card" style="padding: 20px; text-align: center;">
                <span style="font-size: 2rem;">📝</span>
                <h3 style="font-size: 1.8rem; color: #2563eb; margin: 10px 0 5px;"><?= number_format($total_bookings) ?></h3>
                <p style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Total Booking</p>
            </div>
            
            <div class="court-card" style="padding: 20px; text-align: center;">
                <span style="font-size: 2rem;">💰</span>
                <h3 style="font-size: 1.4rem; color: #16a34a; margin: 10px 0 5px;">Rp <?= number_format($total_income, 0, ',', '.') ?></h3>
                <p style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Total Pendapatan</p>
            </div>

            <div class="court-card" style="padding: 20px; text-align: center;">
                <span style="font-size: 2rem;">👥</span>
                <h3 style="font-size: 1.8rem; color: #0284c7; margin: 10px 0 5px;"><?= number_format($total_users) ?></h3>
                <p style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Pelanggan</p>
            </div>

            <div class="court-card" style="padding: 20px; text-align: center;">
                <span style="font-size: 2rem;">🏟️</span>
                <h3 style="font-size: 1.8rem; color: #f59e0b; margin: 10px 0 5px;"><?= number_format($total_courts) ?></h3>
                <p style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Jumlah Lapangan</p>
            </div>
        </div>

        <!-- Tabel Data Seluruh Transaksi Booking -->
        <div class="table-container">
            <h2 style="color: #0f172a; font-size: 1.3rem; margin-bottom: 20px;">Kelola Semua Pesanan Booking</h2>

            <?php if ($all_bookings->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pemesan</th>
                            <th>Lapangan</th>
                            <th>Tgl Main</th>
                            <th>Jam</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = $all_bookings->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['fullname']) ?></strong><br>
                                    <span style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($row['email']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($row['court_name']) ?></td>
                                <td><?= date('d-m-Y', strtotime($row['booking_date'])) ?></td>
                                <td><?= substr($row['start_time'], 0, 5) ?> WIB</td>
                                <td><strong>Rp <?= number_format($row['total_price'], 0, ',', '.') ?></strong></td>
                                <td>
                                    <?php if ($row['status'] === 'confirmed'): ?>
                                        <span style="background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:10px; font-size:0.75rem; font-weight:bold;">CONFIRMED</span>
                                    <?php elseif ($row['status'] === 'cancelled'): ?>
                                        <span style="background:#fee2e2; color:#b91c1c; padding:3px 8px; border-radius:10px; font-size:0.75rem; font-weight:bold;">CANCELLED</span>
                                    <?php else: ?>
                                        <span style="background:#fef3c7; color:#b45309; padding:3px 8px; border-radius:10px; font-size:0.75rem; font-weight:bold;">PENDING</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display:flex; gap:4px;">
                                        <?php if ($row['status'] !== 'confirmed'): ?>
                                            <a href="admin_dashboard.php?action=confirm&booking_id=<?= $row['id'] ?>" class="btn-action" style="background:#22c55e;">Setuju</a>
                                        <?php endif; ?>
                                        <?php if ($row['status'] !== 'cancelled'): ?>
                                            <a href="admin_dashboard.php?action=cancel&booking_id=<?= $row['id'] ?>" class="btn-action" style="background:#f97316;">Batal</a>
                                        <?php endif; ?>
                                        <a href="admin_dashboard.php?action=delete&booking_id=<?= $row['id'] ?>" onclick="return confirm('Hapus pesanan ini?')" class="btn-action" style="background:#ef4444;">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align:center; color:#64748b; padding:20px;">Belum ada pesanan masuk.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer style="margin-top: 50px; background: #0f172a; color: #94a3b8; padding: 20px; text-align: center;">
        <p>&copy; <?= date('Y') ?> Futsal Zone Indonesia - Panel Pengelola.</p>
    </footer>

</body>
</html>