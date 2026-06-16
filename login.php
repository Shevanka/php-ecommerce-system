<?php
require_once __DIR__ . '/config/session.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Penjualan Online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="page-auth">
<main class="section">
    <div class="container">
        <div class="auth-card">
            <span class="auth-eyebrow">Penjualan Online</span>
            <h1 class="auth-title">Masuk ke Akun</h1>

            <?php if ($flash = getFlash()): ?>
                <div class="auth-alert<?= $flash['type'] === 'success' ? ' is-success' : '' ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="proses/proses_login.php">
                <div class="form-group">
                    <label for="username">Nama atau Email</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="auth-submit">Login</button>
            </form>

            <p class="auth-footer">Belum punya akun? <a href="register.php">Daftar di sini</a>.</p>
        </div>
    </div>
</main>
</body>
</html>