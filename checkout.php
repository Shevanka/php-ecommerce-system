<?php
declare(strict_types=1);

// ── Bootstrap ──────────────────────────────────────────
require_once __DIR__ . '/config/session.php';
requireLogin();

// ── Guard ──────────────────────────────────────────────
if (empty($_SESSION['cart'])) {
    setFlash('warning', 'Keranjang belanja kosong.');
    header('Location: /ta2/cart.php');
    exit;
}

// ── Data ───────────────────────────────────────────────
$cart  = $_SESSION['cart'];
$total = 0.0;

foreach ($cart as $item) {
    $total += (float) $item['harga'] * (int) $item['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Penjualan Online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">    <link rel="stylesheet" href="/ta2/assets/css/style.css">
    <style>
        /* ── Checkout Layout ── */
        .checkout-wrapper {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2rem;
            align-items: start;
        }

        /* ── Form Card ── */
        .checkout-form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .form-block {
            padding: 1.75rem 2rem;
        }

        .form-block + .form-block {
            border-top: 1px solid var(--border);
        }

        .form-block-title {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--primary);
            margin: 0 0 1.25rem;
        }

        .form-block-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .field {
            margin-bottom: 1.1rem;
        }

        .field:last-child {
            margin-bottom: 0;
        }

        .field label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.4rem;
        }

        .field .optional {
            font-weight: 400;
            color: var(--muted);
            font-size: 0.8rem;
            margin-left: 0.25rem;
        }

        .field input,
        .field textarea,
        .field select {
            width: 100%;
            padding: 0.65rem 0.9rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font: inherit;
            font-size: 0.95rem;
            background: var(--bg);
            color: var(--text);
            transition: border-color 0.15s, box-shadow 0.15s;
            box-sizing: border-box;
        }

        .field input:focus,
        .field textarea:focus,
        .field select:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--surface);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .field textarea {
            resize: vertical;
            min-height: 90px;
        }

        .field select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%235c6370' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.9rem center;
            padding-right: 2.25rem;
            cursor: pointer;
        }

        .form-footer {
            padding: 1.5rem 2rem;
            background: var(--bg);
            border-top: 1px solid var(--border);
        }

        .form-footer .btn {
            width: 100%;
            padding: 0.85rem;
            font-size: 1rem;
            border-radius: 10px;
            justify-content: center;
        }

        /* ── Order Summary ── */
        .order-summary {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            position: sticky;
            top: 5rem;
        }

        .summary-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-header a {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--primary);
        }

        .summary-header a:hover {
            text-decoration: underline;
        }

        .summary-items {
            list-style: none;
            margin: 0;
            padding: 0.5rem 0;
        }

        .summary-items li {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.65rem 1.5rem;
            font-size: 0.875rem;
        }

        .summary-items li + li {
            border-top: 1px solid var(--border);
        }

        .item-name {
            font-weight: 500;
            color: var(--text);
            line-height: 1.4;
        }

        .item-qty {
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 0.15rem;
        }

        .item-price {
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-top: 2px solid var(--border);
            font-size: 0.95rem;
        }

        .summary-total span {
            color: var(--muted);
            font-size: 0.85rem;
        }

        .summary-total strong {
            font-size: 1.2rem;
            color: var(--primary);
        }

        /* ── Responsive ── */
        @media (max-width: 820px) {
            .checkout-wrapper {
                grid-template-columns: 1fr;
            }

            .order-summary {
                position: static;
                order: -1;
            }

            .form-block {
                padding: 1.25rem 1.25rem;
            }

            .form-footer {
                padding: 1.25rem;
            }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/includes/navbar.php'; ?>

<main class="section">
    <div class="container">

        <div class="section-head" style="margin-bottom: 2rem;">
            <h1 style="font-size:1.75rem; margin:0 0 0.25rem;">Checkout</h1>
            <p style="margin:0;">Isi data penerima lalu pilih metode pembayaran.</p>
        </div>

        <?php require __DIR__ . '/includes/alert.php'; ?>

        <div class="checkout-wrapper">

            <!-- ── Form ── -->
            <div class="checkout-form-card">
                <form action="proses/proses_checkout.php" method="post" novalidate>

                    <!-- Data Penerima -->
                    <div class="form-block">
                        <p class="form-block-title">Data Penerima</p>

                        <div class="field">
                            <label for="nama_penerima">Nama Penerima</label>
                            <input
                                type="text"
                                id="nama_penerima"
                                name="nama_penerima"
                                placeholder="Nama lengkap penerima"
                                autocomplete="name"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="alamat">Alamat Lengkap</label>
                            <textarea
                                id="alamat"
                                name="alamat"
                                rows="3"
                                placeholder="Nama jalan, nomor rumah, kota, kode pos"
                                autocomplete="street-address"
                                required
                            ></textarea>
                        </div>

                        <div class="field">
                            <label for="telepon">Nomor Telepon</label>
                            <input
                                type="tel"
                                id="telepon"
                                name="telepon"
                                placeholder="081234567890"
                                autocomplete="tel"
                                required
                            >
                        </div>
                    </div>

                    <!-- Pembayaran -->
                    <div class="form-block">
                        <p class="form-block-title">Pembayaran</p>

                        <div class="field">
                            <label for="metode_pembayaran">Metode Pembayaran</label>
                            <select id="metode_pembayaran" name="metode_pembayaran" required>
                                <option value="" disabled selected>Pilih metode pembayaran</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="cod">Bayar di Tempat (COD)</option>
                                <option value="kartu_kredit">Kartu Kredit</option>
                            </select>
                        </div>

                        <div class="field">
                            <label for="catatan">
                                Catatan
                                <span class="optional">(opsional)</span>
                            </label>
                            <textarea
                                id="catatan"
                                name="catatan"
                                rows="2"
                                placeholder="Misal: titip di resepsionis"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">Buat Pesanan</button>
                    </div>

                </form>
            </div>

            <!-- ── Order Summary ── -->
            <aside class="order-summary">
                <div class="summary-header">
                    <span>Ringkasan Pesanan</span>
                    <a href="cart.php">Ubah</a>
                </div>

                <ul class="summary-items">
                    <?php foreach ($cart as $item): ?>
                        <li>
                            <div>
                                <div class="item-name">
                                    <?= htmlspecialchars($item['nama_produk'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div class="item-qty">
                                    <?= (int) $item['jumlah'] ?> &times;
                                    Rp <?= number_format((float) $item['harga'], 0, ',', '.') ?>
                                </div>
                            </div>
                            <div class="item-price">
                                Rp <?= number_format((float) $item['harga'] * (int) $item['jumlah'], 0, ',', '.') ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="summary-total">
                    <span>Total Pembayaran</span>
                    <strong>Rp <?= number_format($total, 0, ',', '.') ?></strong>
                </div>
            </aside>

        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>