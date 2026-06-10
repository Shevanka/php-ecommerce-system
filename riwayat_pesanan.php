<?php
session_start();

function addOrderToHistory($orderData)
{
    $orderHistory = isset($_COOKIE['order_history']) ? json_decode($_COOKIE['order_history'], true) : [];

    $orderData['order_id'] = uniqid('ORDER_');
    $orderData['order_date'] = date('Y-m-d H:i:s');

    $orderHistory[] = $orderData;

    setcookie('order_history', json_encode($orderHistory), time() + (30 * 24 * 60 * 60), '/');
    $_COOKIE['order_history'] = json_encode($orderHistory);
}

function getOrderHistory()
{
    return isset($_COOKIE['order_history']) ? json_decode($_COOKIE['order_history'], true) : [];
}

function clearOrderHistory()
{
    setcookie('order_history', '', time() - 3600, '/');
    unset($_COOKIE['order_history']);
}

$orderHistory = getOrderHistory();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .order-card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .order-id {
            font-weight: bold;
            color: #007bff;
        }

        .order-date {
            color: #666;
            font-size: 0.9em;
        }

        .order-items {
            margin: 10px 0;
        }

        .item-list {
            margin-left: 20px;
        }

        .item {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .item:last-child {
            border-bottom: none;
        }

        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #ddd;
            font-size: 1.1em;
        }

        .empty-message {
            text-align: center;
            color: #999;
            padding: 40px 20px;
            font-size: 1.1em;
        }

        .clear-history-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 20px;
        }

        .clear-history-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>📋 Riwayat Pesanan Saya</h1>

        <?php if (empty($orderHistory)): ?>
            <div class="empty-message">
                Belum ada riwayat pesanan. Lakukan pembelian untuk melihat pesanan Anda di sini.
            </div>
        <?php else: ?>
            <?php foreach (array_reverse($orderHistory) as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-id">ID Pesanan: <?php echo htmlspecialchars($order['order_id']); ?></div>
                            <div class="order-date">Tanggal: <?php echo htmlspecialchars($order['order_date']); ?></div>
                        </div>
                    </div>

                    <div class="order-items">
                        <strong>Item Pesanan:</strong>
                        <div class="item-list">
                            <?php if (isset($order['items']) && is_array($order['items'])): ?>
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="item">
                                        <strong><?php echo htmlspecialchars($item['name'] ?? 'Produk'); ?></strong><br>
                                        Jumlah: <?php echo htmlspecialchars($item['quantity'] ?? 1); ?> |
                                        Harga: Rp <?php echo number_format($item['price'] ?? 0, 0, ',', '.'); ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (isset($order['total'])): ?>
                        <div class="total">
                            Total: Rp <?php echo number_format($order['total'], 0, ',', '.'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <form method="POST">
                <button type="submit" name="clear_history" class="clear-history-btn">Hapus Riwayat Pesanan</button>
            </form>
        <?php endif; ?>
    </div>

    <?php
    // Handle clear history request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_history'])) {
        clearOrderHistory();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    ?>
</body>

</html>