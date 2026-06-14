<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

$id = (int) ($_POST['id'] ?? 0);
$nama = trim($_POST['nama'] ?? '');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id < 1 || $nama === '') {
    setFlash('danger', 'Data kategori tidak valid.');
    header('Location: ../admin/kategori.php');
    exit;
}

try {
    $stmt = $conn->prepare('UPDATE kategori SET nama = ? WHERE id = ?');
    $stmt->bind_param('si', $nama, $id);
    $stmt->execute();
    setFlash('success', 'Kategori berhasil diperbarui.');
    header('Location: ../admin/kategori.php');
} catch (mysqli_sql_exception $e) {
    setFlash('danger', $e->getCode() === 1062 ? 'Nama kategori sudah digunakan.' : 'Gagal memperbarui kategori.');
    header('Location: ../admin/edit_kategori.php?id=' . $id);
}
exit;
