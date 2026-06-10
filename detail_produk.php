<?php
require_once 'koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $koneksi->prepare('SELECT id, nama, harga, stok, deskripsi, gambar FROM produk WHERE id = ? LIMIT 1');
if (!$stmt) {
    header('Location: index.php');
    exit;
}

$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$produk = $result->fetch_assoc();
$stmt->close();

if (!$produk) {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Produk - <?= htmlspecialchars($produk['nama']) ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-4">
        <a href="index.php" class="btn btn-secondary mb-3">Kembali</a>
        <div class="card mb-4">
            <div class="row g-0">
                <div class="col-md-4">
                    <?php if (!empty($produk['gambar'])): ?>
                        <img src="<?= htmlspecialchars($produk['gambar']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($produk['nama']) ?>">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100%; min-height: 280px;">
                            <span class="text-muted">Tidak ada gambar</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h1 class="card-title"><?= htmlspecialchars($produk['nama']) ?></h1>
                        <p class="card-text h4 text-success">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                        <p class="card-text"><strong>Stok:</strong> <?= htmlspecialchars($produk['stok']) ?></p>
                        <p class="card-text"><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>
                        <a href="keranjang.php?id=<?= $produk['id'] ?>&action=add" class="btn btn-primary">Tambah ke Keranjang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
