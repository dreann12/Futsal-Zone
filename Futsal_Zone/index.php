<?php
session_start();
require_once 'config/db.php';

// Fetch all courts
$sql = "SELECT * FROM courts ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futsal Zone - Sewa Lapangan Futsal Indoor Online</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">⚽ Futsal <span>Zone</span></a>
        <ul class="nav-links">
            <li><a href="index.php">Sewa Lapangan</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="my_bookings.php">Riwayat Booking</a></li>
                <li style="color: #38bdf8; font-weight: 600;">Halo, <?= htmlspecialchars($_SESSION['fullname']) ?></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Masuk</a></li>
                <li><a href="register.php" class="btn-auth">Daftar</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="hero">
        <h1>DAFTAR LAPANGAN FUTSAL ZONE</h1>
        <p>Informasi lengkap venue indoor, kualitas bola, fasilitas modern, serta harga sewa per jam secara real-time.</p>
    </div>

    <div class="container">
        <div class="court-grid">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="court-card">
                    <div class="court-img-wrapper">
                        <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        <span class="court-type-badge"><?= htmlspecialchars($row['type']) ?></span>
                    </div>
                    <div class="court-body">
                        <h3 class="court-title"><?= htmlspecialchars($row['name']) ?></h3>
                        <div class="court-price">
                            Rp <?= number_format($row['price_per_hour'], 0, ',', '.') ?> <span>/ jam</span>
                        </div>
                        
                        <div class="info-section">
                            <div class="info-title">📌 Deskripsi:</div>
                            <p style="color: #64748b; font-size:0.85rem; line-height: 1.4;"><?= htmlspecialchars($row['description']) ?></p>
                        </div>

                        <div class="info-section">
                            <div class="info-title">⚽ Kualitas & Merk Bola:</div>
                            <p style="font-size:0.85rem; color:#1e293b; font-weight:600;"><?= htmlspecialchars($row['ball_quality']) ?></p>
                        </div>

                        <div class="info-section">
                            <div class="info-title">🏢 Fasilitas Venue:</div>
                            <div class="facility-tags">
                                <?php 
                                $facs = explode(',', $row['facilities']);
                                foreach($facs as $f): 
                                ?>
                                    <span class="tag"><?= trim(htmlspecialchars($f)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <a href="booking.php?id=<?= $row['id'] ?>" class="btn-book">Booking Sekarang</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> Futsal Zone Indonesia. All Rights Reserved.</p>
    </footer>
</body>
</html>
