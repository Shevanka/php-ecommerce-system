<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/kategori.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
if ($nama === '') {
    setFlash('danger', 'Nama kategori wajib diisi.');
    header('Location: ../admin/tambah_kategori.php');
    exit;
}

try {
    $stmt = $conn->prepare('INSERT INTO kategori (nama) VALUES (?)');
    $stmt->bind_param('s', $nama);
    $stmt->execute();
    setFlash('success', 'Kategori berhasil ditambahkan.');
    header('Location: ../admin/kategori.php');
} catch (mysqli_sql_exception $e) {
    setFlash('danger', $e->getCode() === 1062 ? 'Nama kategori sudah digunakan.' : 'Gagal menambah kategori.');
    header('Location: ../admin/tambah_kategori.php?old=' . urlencode($nama));
}
exit;
