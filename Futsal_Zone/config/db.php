<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "futsal_zone";

// Buat koneksi ke MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Cek jika koneksi gagal
if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}
?>