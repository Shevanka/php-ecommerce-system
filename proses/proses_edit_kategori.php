<?php
<<<<<<< HEAD
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
=======
/**
 * Proses Edit Kategori
 * Menangani update kategori (Admin)
 */

require_once '../config/database.php';
require_once '../config/session.php';

try {
    // Validasi autentikasi dan autorisasi
    requireAdmin();

    // Validasi method request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method tidak diizinkan');
    }

    // Ambil dan validasi input
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nama_kategori = isset($_POST['nama_kategori']) ? trim($_POST['nama_kategori']) : '';
    $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';

    // Validasi input
    if ($id <= 0) {
        throw new Exception('ID kategori tidak valid');
    }

    if (empty($nama_kategori)) {
        throw new Exception('Nama kategori harus diisi');
    }

    // Ambil data kategori lama
    $query = "SELECT * FROM kategori WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Kategori tidak ditemukan');
    }

    // Periksa duplikat kategori (tidak termasuk kategori yang sedang diedit)
    $checkQuery = "SELECT id FROM kategori WHERE nama_kategori = ? AND id != ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('si', $nama_kategori, $id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('Kategori dengan nama yang sama sudah ada');
    }

    // Update kategori
    $updateQuery = "UPDATE kategori SET nama_kategori = ?, deskripsi = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    
    if (!$stmt) {
        throw new Exception('Preparasi query gagal: ' . $conn->error);
    }

    $stmt->bind_param('ssi', $nama_kategori, $deskripsi, $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Gagal mengupdate kategori: ' . $stmt->error);
    }

    // Flash message
    setFlash('kategori', 'Kategori berhasil diperbarui', 'success');

    // Redirect ke halaman kategori
    header('Location: ../admin/kategori.php');
    exit;

} catch (Exception $e) {
    // Log error dan set flash message
    error_log('Edit Kategori Error: ' . $e->getMessage());
    setFlash('kategori', $e->getMessage(), 'danger');
    header('Location: ../admin/edit_kategori.php?id=' . (isset($_POST['id']) ? intval($_POST['id']) : ''));
    exit;
}
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
