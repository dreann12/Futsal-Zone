<?php
session_start();
require_once 'config/db.php';

// Proteksi Halaman: Hanya untuk User yang sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Ambil Total Booking User
$stmt1 = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE user_id = ?");
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$total_my_bookings = $stmt1->get_result()->fetch_assoc()['total'];

// 2. Ambil Total Pengeluaran User
$stmt2 = $conn->prepare("SELECT SUM(total_price) as total FROM bookings WHERE user_id = ? AND status = 'confirmed'");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$total_my_spent = $stmt2->get_result()->fetch_assoc()['total'] ?? 0;

// 3. Ambil Riwayat Booking milik User
$stmt3 = $conn->prepare("
    SELECT b.*, c.name as court_name 
    FROM bookings b 
    JOIN courts c ON b.court_id = c.id 
    WHERE b.user_id = ? 
    ORDER BY b.created_at DESC
");
$stmt3->bind_param("i", $user_id);
$stmt3->execute();
$my_bookings = $stmt3->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - Futsal Zone</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
        }
        .status-confirmed { background: #dcfce7; color: #15803d; }
        .status-pending { background: #fef3c7; color: #b45309; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>

    <!-- Navbar User -->
    <nav class="navbar">
        <a href="dashboard.php" class="logo">⚽ Futsal <span>Zone</span></a>
        <ul class="nav-links">
            <li><a href="dashboard.php">Beranda</a></li>
            <li><a href="user_dashboard.php" style="color: #38bdf8; font-weight:700;">Dashboard Saya</a></li>
            <li style="color: #38bdf8; font-weight: 600;">Halo, <?= htmlspecialchars($_SESSION['fullname']) ?></li>
            <li><a href="logout.php" class="btn-logout">Logout</a></li>
        </ul>
    </nav>

    <div class="container" style="margin-top: 30px;">
        <!-- Card Header User -->
        <div class="dashboard-header">
            <h1 style="margin:0 0 10px 0;">Selamat Datang, <?= htmlspecialchars($_SESSION['fullname']) ?>! 👋</h1>
            <p style="margin:0; opacity: 0.9;">Pantau status reservasi lapangan dan jadwal main Anda di Futsal Zone.</p>
        </div>

        <!-- Ringkasan Statistik User -->
        <div class="court-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 30px;">
            <div class="court-card" style="padding: 20px; text-align: center;">
                <span style="font-size: 2rem;">⚽</span>
                <h3 style="font-size: 1.8rem; color: #0284c7; margin: 10px 0 5px;"><?= number_format($total_my_bookings) ?></h3>
                <p style="color: #64748b; font-size: 0.9rem; font-weight: 600;">Total Booking Saya</p>
            </div>
            
            <div class="court-card" style="padding: 20px; text-align: center;">
                <span style="font-size: 2rem;">💳</span>
                <h3 style="font-size: 1.5rem; color: #16a34a; margin: 10px 0 5px;">Rp <?= number_format($total_my_spent, 0, ',', '.') ?></h3>
                <p style="color: #64748b; font-size: 0.9rem; font-weight: 600;">Total Transaksi Sukses</p>
            </div>
        </div>

        <!-- Tabel Riwayat Booking User -->
        <div class="table-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: #0f172a; font-size: 1.3rem;">Riwayat Booking Lapangan Saya</h2>
                <a href="index.php#lapangan" class="btn-primary" style="padding: 8px 16px; font-size: 0.9rem; text-decoration: none; background: #22c55e; color: white; border-radius: 6px; font-weight: bold;">+ Pesan Lapangan Baru</a>
            </div>

            <?php if ($my_bookings->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lapangan</th>
                            <th>Tanggal Main</th>
                            <th>Jam</th>
                            <th>Durasi</th>
                            <th>Total Biaya</th>
                            <th>Status Konfirmasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = $my_bookings->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['court_name']) ?></strong></td>
                                <td><?= date('d-m-Y', strtotime($row['booking_date'])) ?></td>
                                <td><?= substr($row['start_time'], 0, 5) ?> WIB</td>
                                <td><?= $row['duration_hours'] ?> Jam</td>
                                <td><strong style="color: #2563eb;">Rp <?= number_format($row['total_price'], 0, ',', '.') ?></strong></td>
                                <td>
                                    <?php if ($row['status'] === 'confirmed'): ?>
                                        <span class="status-badge status-confirmed">BERHASIL / DIKONFIRMASI</span>
                                    <?php elseif ($row['status'] === 'cancelled'): ?>
                                        <span class="status-badge status-cancelled">DIBATALKAN</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending">MENUNGGU KONFIRMASI</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #64748b;">
                    <p style="font-size: 1.1rem; margin-bottom: 15px;">Anda belum pernah melakukan booking online.</p>
                    <a href="dashboard.php#lapangan" style="color: #2563eb; font-weight: bold; text-decoration: underline;">Klik di sini untuk melihat pilihan lapangan</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer style="margin-top: 50px; background: #0f172a; color: #94a3b8; padding: 20px; text-align: center;">
        <p>&copy; <?= date('Y') ?> Futsal Zone Indonesia.</p>
    </footer>

</body>
</html>