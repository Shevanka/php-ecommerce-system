<?php
/**
 * Session Configuration
 * Menginisialisasi dan mengatur session untuk aplikasi
 */

// Konfigurasi session
ini_set('session.cookie_lifetime', 86400); // 24 jam
ini_set('session.gc_maxlifetime', 86400);  // 24 jam

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function appUrl(string $path = ''): string
{
    $basePath = rtrim((string) (getenv('APP_BASE_PATH') ?: '/penjualan-online'), '/');
    return $basePath . '/' . ltrim($path, '/');
}

// Flash message helper
function setFlash($key, $message, $type = 'success')
{
    if (func_num_args() === 2 && in_array($key, ['success', 'danger', 'error', 'warning', 'info'], true)) {
        $type = $key;
    }

    $_SESSION['flash'] = [
        'key' => $key,
        'message' => $message,
        'type' => $type // success, error, warning, info
    ];
}

function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Check authentication
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function cartCount(): int
{
    return array_sum(array_map(
        static fn(array $item): int => (int) ($item['jumlah'] ?? 0),
        $_SESSION['cart'] ?? []
    ));
}

// Check if user is admin
function isAdmin()
{
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect if not logged in
function requireLogin()
{
    if (!isLoggedIn()) {
        setFlash('auth', 'Anda harus login terlebih dahulu', 'warning');
        header('Location: ' . appUrl('login.php'));
        exit;
    }
}

// Redirect if not admin
function requireAdmin()
{
    if (!isAdmin()) {
        setFlash('auth', 'Anda tidak memiliki akses ke halaman ini', 'danger');
        header('Location: ' . appUrl('index.php'));
        exit;
    }
}
