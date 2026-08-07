<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = (int) (getenv('DB_PORT') ?: 3306);
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db = getenv('DB_NAME') ?: 'penjualan_online';

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    throw new RuntimeException('Koneksi database gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
