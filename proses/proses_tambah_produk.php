<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
requireAdmin();

function simpanGambar(array $file): ?string
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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new RuntimeException('Method tidak diizinkan.');
    $nama = trim($_POST['nama'] ?? '');
    $kategoriId = (int) ($_POST['kategori_id'] ?? 0);
    $harga = (float) ($_POST['harga'] ?? -1);
    $stok = (int) ($_POST['stok'] ?? -1);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    if ($nama === '' || $kategoriId < 1 || $harga < 0 || $stok < 0) throw new RuntimeException('Data produk tidak valid.');
    $gambar = simpanGambar($_FILES['gambar'] ?? []);
    $stmt = $conn->prepare('INSERT INTO produk (kategori_id, nama, deskripsi, harga, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('issdis', $kategoriId, $nama, $deskripsi, $harga, $stok, $gambar);
    $stmt->execute();
    setFlash('success', 'Produk berhasil ditambahkan.');
    header('Location: ../admin/produk.php');
} catch (Throwable $e) {
    setFlash('danger', $e->getMessage());
    header('Location: ../admin/tambah_produk.php');
}
exit;
