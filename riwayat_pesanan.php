<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
requireLogin();

$stmt = $conn->prepare('SELECT id, total, status, created_at FROM pesanan WHERE user_id = ? ORDER BY created_at DESC');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Riwayat Pesanan';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<main class="section"><div class="container">
    <div class="section-head"><h1>Riwayat Pesanan</h1></div>
    <?php require __DIR__ . '/includes/alert.php'; ?>
    <?php if ($orders === []): ?><div class="empty-state"><p>Belum ada pesanan.</p></div><?php endif; ?>
    <?php foreach ($orders as $order): ?>
        <article class="feature-card" style="margin-bottom: 1rem;">
            <h3>Pesanan #<?= (int) $order['id'] ?></h3>
            <p>Status: <?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></p>
            <p>Total: Rp <?= number_format((float) $order['total'], 0, ',', '.') ?></p>
            <small><?= htmlspecialchars($order['created_at'], ENT_QUOTES, 'UTF-8') ?></small>
        </article>
    <?php endforeach; ?>
</div></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
