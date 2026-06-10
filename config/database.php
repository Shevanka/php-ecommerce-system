<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "penjualan_online";

// Membuat koneksi dengan mysqli
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Mengecek koneksi database
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($koneksi, "utf8");

if (!function_exists('db')) {
    function db(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $host = "localhost";
        $user = "root";
        $pass = "";
        $db = "penjualan_online";

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $db);

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException('Koneksi PDO gagal: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
