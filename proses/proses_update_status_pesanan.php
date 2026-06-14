<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

$id = (int) ($_POST['id'] ?? $_POST['pesanan_id'] ?? 0);
$status = $_POST['status'] ?? '';
$valid = ['pending', 'diproses', 'dikirim', 'selesai', 'batal'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id < 1 || !in_array($status, $valid, true)) {
    setFlash('danger', 'Data status pesanan tidak valid.');
} else {
    $stmt = $conn->prepare('UPDATE pesanan SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    setFlash('success', 'Status pesanan berhasil diperbarui.');
}
header('Location: ../admin/pesanan.php');
exit;
