⚽ Futsal Zone - Sistem Reservasi Lapangan Futsal Online

Futsal Zone adalah aplikasi berbasis web yang memudahkan pelanggan untuk melihat informasi lapangan futsal indoor, mengecek harga sewa, serta melakukan reservasi lapangan secara real-time. Aplikasi ini juga dilengkapi dengan panel admin untuk mengelola pesanan dan statistik venue.

---

🚀 Fitur Utama

👤 Fitur User / Pelanggan
- Katalog Lapangan: Melihat daftar lapangan lengkap dengan jenis lantai (Matras, Rumput Sintetis, Parquet), harga sewa, deskripsi, dan fasilitas venue.
- Autentikasi Akun: Fitur Registrasi dan Login akun pelanggan.
- Booking Lapangan: Melakukan pemesanan lapangan futsal sesuai jadwal yang diinginkan.
- Riwayat Booking: Menampilkan status dan daftar pemesanan yang telah dibuat pengguna.

🛡️ Fitur Admin
- Dashboard Admin: Ringkasan statistik total booking, total pendapatan, jumlah pelanggan, dan jumlah lapangan.
- Kelola Pesanan: Memverifikasi, mengonfirmasi, membatalkan, atau menghapus pesanan masuk.

---

🛠️ Teknologi & Bahasa Pemrograman

Proyek ini dibangun menggunakan teknologi berikut:
- PHP: Bahasa pemrogram backend & logika aplikasi
- MySQL / SQL: Database penyimpanan data pengguna, lapangan, dan transaksi booking
- HTML: Struktur dan elemen antarmuka halaman web
- CSS: Penataan tampilan dan gaya UI (*Responsive Design*)
- XAMPP / Apache: Web server lokal untuk menjalankan PHP & MySQL

---

📁 Struktur Folder Project

text
Futsal_Zone/
├── config/
│   └── db.php            # Koneksi ke database MySQL
├── css/
│   └── style.css          # Styling utama seluruh halaman web
├── admin_dashboard.php   # Panel manajemen admin
├── booking.php           # Form alur pemesanan lapangan
├── database.sql          # File skema database MySQL
├── index.php             # Halaman utama / Katalog lapangan
├── login.php             # Halaman masuk sistem
├── logout.php            # Halaman keluar sistem
├── my_bookings.php       # Halaman riwayat reservasi pelanggan
├── register.php          # Halaman pendaftaran akun baru
└── user_dashboard.php    # Dashboard khusus pengguna terdaftar
