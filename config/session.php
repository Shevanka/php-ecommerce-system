<?php

ini_set('session.cookie_lifetime', 86400);
ini_set('session.gc_maxlifetime', 86400);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function setFlash(string $key, string $message, string $type = 'success'): void
{
    if (func_num_args() === 2 && in_array($key, ['success', 'danger', 'error', 'warning', 'info'], true)) {
        $type = $key;
    }

    $_SESSION['flash'] = [
        'key' => $key,
        'message' => $message,
        'type' => $type,
    ];
}

function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function cartCount(): int
{
    return array_sum(array_map(
        static fn(array $item): int => (int) ($item['jumlah'] ?? 0),
        $_SESSION['cart'] ?? []
    ));
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('warning', 'Anda harus login terlebih dahulu.');
        header('Location: /TA2/login.php');
        exit;
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        setFlash('danger', 'Anda tidak memiliki akses ke halaman ini.');
        header('Location: /TA2/index.php');
        exit;
    }
}
