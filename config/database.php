<?php
<<<<<<< HEAD

declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = (int) (getenv('DB_PORT') ?: 3306);
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'penjualan_online';

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    throw new RuntimeException('Koneksi database gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
=======
/**
 * Database Configuration
 * Menghubungkan aplikasi dengan database MySQL
 */

$host = "localhost";
$user = "root";
$pass = "";
$db = "penjualan_online";

// Membuat koneksi dengan mysqli
$conn = mysqli_connect($host, $user, $pass, $db);

// Mengecek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($conn, "utf8");

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
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
