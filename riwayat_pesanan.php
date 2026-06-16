<?php
declare(strict_types=1);

// ── Bootstrap ──────────────────────────────────────────
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
requireLogin();

// ── Data ───────────────────────────────────────────────
$stmt = $conn->prepare('SELECT id, total, status, created_at FROM pesanan WHERE user_id = ? ORDER BY created_at DESC');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$statusLabels = [
    'pending'  => 'Menunggu',
    'diproses' => 'Diproses',
    'dikirim'  => 'Dikirim',
    'selesai'  => 'Selesai',
    'batal'    => 'Dibatalkan',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan | Penjualan Online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/ta2/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/includes/navbar.php'; ?>

<main class="section">
    <div class="container">
        <div class="section-head">
            <h1>Riwayat Pesanan</h1>
        </div>

        <?php require __DIR__ . '/includes/alert.php'; ?>

        <?php if ($orders === []): ?>
            <div class="empty-state">
                <p>Belum ada pesanan.</p>
                <a href="index.php#produk" class="btn btn-primary">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <?php $statusKey = $order['status']; ?>
                <article class="order-card">
                    <div>
                        <span class="order-card-id">PESANAN #<?= str_pad((string) $order['id'], 5, '0', STR_PAD_LEFT) ?></span>
                        <h3>Rp <?= number_format((float) $order['total'], 0, ',', '.') ?></h3>
                        <span class="order-card-date"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></span>
                    </div>
                    <span class="order-stamp is-<?= htmlspecialchars($statusKey, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($statusLabels[$statusKey] ?? $statusKey, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>