<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

function simpanGambarEdit(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2 * 1024 * 1024) throw new RuntimeException('Upload gambar gagal atau melebihi 2MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) throw new RuntimeException('Format gambar harus JPG, PNG, atau WEBP.');
    $dir = __DIR__ . '/../assets/img/produk';
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $name = bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) throw new RuntimeException('Gagal menyimpan gambar.');
    return $name;
}

try {
    $id = (int) ($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $kategoriId = (int) ($_POST['kategori_id'] ?? 0);
    $harga = (float) ($_POST['harga'] ?? -1);
    $stok = (int) ($_POST['stok'] ?? -1);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id < 1 || $nama === '' || $kategoriId < 1 || $harga < 0 || $stok < 0) throw new RuntimeException('Data produk tidak valid.');
    $gambar = simpanGambarEdit($_FILES['gambar'] ?? []);
    if ($gambar) {
        $stmt = $conn->prepare('UPDATE produk SET kategori_id=?, nama=?, deskripsi=?, harga=?, stok=?, gambar=? WHERE id=?');
        $stmt->bind_param('issdisi', $kategoriId, $nama, $deskripsi, $harga, $stok, $gambar, $id);
    } else {
        $stmt = $conn->prepare('UPDATE produk SET kategori_id=?, nama=?, deskripsi=?, harga=?, stok=? WHERE id=?');
        $stmt->bind_param('issdii', $kategoriId, $nama, $deskripsi, $harga, $stok, $id);
    }
    $stmt->execute();
    setFlash('success', 'Produk berhasil diperbarui.');
    header('Location: ../admin/produk.php');
} catch (Throwable $e) {
    setFlash('danger', $e->getMessage());
    header('Location: ../admin/edit_produk.php?id=' . (int) ($_POST['id'] ?? 0));
}
exit;
