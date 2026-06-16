<?php
declare(strict_types=1);

// ── Bootstrap ──────────────────────────────────────────
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';

// ── Data ───────────────────────────────────────────────
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: /ta2/index.php');
    exit;
}

$stmt = $conn->prepare('SELECT id, nama, harga, stok, deskripsi, gambar FROM produk WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$produk = $stmt->get_result()->fetch_assoc();

if (!$produk) {
    header('Location: /ta2/index.php');
    exit;
}

$imgPath  = 'assets/img/produk/' . basename($produk['gambar'] ?? '');
$hasImage = !empty($produk['gambar']) && file_exists(__DIR__ . '/' . $imgPath);
$inStock  = (int) $produk['stok'] > 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($produk['nama'], ENT_QUOTES, 'UTF-8') ?> | Penjualan Online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/ta2/assets/css/style.css">
    <style>
        /* ── Detail Layout ── */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: start;
        }

        /* ── Image Panel ── */
        .detail-image {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .detail-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .detail-image-placeholder {
            font-size: 5rem;
            opacity: 0.35;
        }

        /* ── Body Panel ── */
        .detail-body {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .detail-body h1 {
            font-size: clamp(1.4rem, 3vw, 2rem);
            line-height: 1.2;
            letter-spacing: -0.02em;
            margin: 0;
        }

        .detail-price {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.01em;
        }

        .detail-stock {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
        }

        .detail-stock.in-stock {
            background: rgba(5, 150, 105, 0.1);
            color: var(--success);
        }

        .detail-stock.out-of-stock {
            background: rgba(220, 38, 38, 0.08);
            color: #dc2626;
        }

        .detail-desc {
            font-size: 0.95rem;
            color: var(--muted);
            line-height: 1.7;
            margin: 0;
            padding: 1rem;
            background: var(--bg);
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        /* ── Quantity + Add to Cart ── */
        .detail-add-form {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .qty-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .qty-row label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
        }

        .qty-input {
            width: 90px;
            padding: 0.6rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font: inherit;
            font-size: 0.95rem;
            background: var(--surface);
            color: var(--text);
            text-align: center;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .qty-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .detail-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .detail-actions .btn {
            flex: 1;
            min-width: 140px;
            justify-content: center;
            padding: 0.75rem 1rem;
        }

        /* ── Divider ── */
        .detail-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 0;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .detail-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .detail-image {
                aspect-ratio: 4 / 3;
            }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/includes/navbar.php'; ?>

<main class="section">
    <div class="container">

        <?php require __DIR__ . '/includes/alert.php'; ?>

        <div class="detail-grid">

            <!-- ── Image ── -->
            <div class="detail-image">
                <?php if ($hasImage): ?>
                    <img
                        src="/ta2/<?= htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($produk['nama'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                <?php else: ?>
                    <div class="detail-image-placeholder" aria-hidden="true">📦</div>
                <?php endif; ?>
            </div>

            <!-- ── Info + Form ── -->
            <div class="detail-body">

                <h1><?= htmlspecialchars($produk['nama'], ENT_QUOTES, 'UTF-8') ?></h1>

                <div class="detail-price">
                    Rp <?= number_format((float) $produk['harga'], 0, ',', '.') ?>
                </div>

                <div>
                    <?php if ($inStock): ?>
                        <span class="detail-stock in-stock">
                            ✓ Tersedia &mdash; <?= (int) $produk['stok'] ?> unit
                        </span>
                    <?php else: ?>
                        <span class="detail-stock out-of-stock">
                            ✕ Stok habis
                        </span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($produk['deskripsi'])): ?>
                    <p class="detail-desc">
                        <?= nl2br(htmlspecialchars($produk['deskripsi'], ENT_QUOTES, 'UTF-8')) ?>
                    </p>
                <?php endif; ?>

                <hr class="detail-divider">

                <?php if ($inStock): ?>
                    <form class="detail-add-form" action="proses/proses_cart.php" method="post">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="produk_id" value="<?= (int) $produk['id'] ?>">

                        <div class="qty-row">
                            <label for="jumlah">Jumlah</label>
                            <input
                                class="qty-input"
                                type="number"
                                id="jumlah"
                                name="jumlah"
                                min="1"
                                max="<?= (int) $produk['stok'] ?>"
                                value="1"
                            >
                        </div>

                        <div class="detail-actions">
                            <button type="submit" class="btn btn-primary">+ Tambah ke Keranjang</button>
                            <a href="index.php" class="btn btn-outline">← Katalog</a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="detail-actions">
                        <a href="index.php" class="btn btn-outline">← Kembali ke Katalog</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>