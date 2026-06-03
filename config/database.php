<?php
/**
 * Database Configuration
 * Menghubungkan aplikasi dengan database MySQL
 */

$host = "localhost";
$user = "root";
$pass = "";
$db   = "penjualan_online";

// Membuat koneksi dengan mysqli
$conn = mysqli_connect($host, $user, $pass, $db);

// Mengecek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($conn, "utf8");
