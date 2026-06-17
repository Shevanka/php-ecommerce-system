<?php
<<<<<<< HEAD

ini_set('session.cookie_lifetime', 86400);
ini_set('session.gc_maxlifetime', 86400);

=======
/**
 * Session Configuration
 * Menginisialisasi dan mengatur session untuk aplikasi
 */

// Konfigurasi session
ini_set('session.cookie_lifetime', 86400); // 24 jam
ini_set('session.gc_maxlifetime', 86400);  // 24 jam

// Mulai session jika belum dimulai
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

<<<<<<< HEAD
function setFlash(string $key, string $message, string $type = 'success'): void
{
    if (func_num_args() === 2 && in_array($key, ['success', 'danger', 'error', 'warning', 'info'], true)) {
        $type = $key;
    }

    $_SESSION['flash'] = [
        'key'     => $key,
        'message' => $message,
        'type'    => $type,
    ];
}

function getFlash(): ?array
=======
// Flash message helper
function setFlash($key, $message, $type = 'success')
{
    $_SESSION['flash'] = [
        'key' => $key,
        'message' => $message,
        'type' => $type // success, error, warning, info
    ];
}

function getFlash()
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

<<<<<<< HEAD
function isLoggedIn(): bool
=======
// Check authentication
function isLoggedIn()
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

<<<<<<< HEAD
function isAdmin(): bool
=======
// Check if user is admin
function isAdmin()
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
{
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

<<<<<<< HEAD
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
=======
// Redirect if not logged in
function requireLogin()
{
    if (!isLoggedIn()) {
        setFlash('auth', 'Anda harus login terlebih dahulu', 'warning');
        header('Location: /login.php');
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
        exit;
    }
}

<<<<<<< HEAD
function requireAdmin(): void
{
    if (!isAdmin()) {
        setFlash('danger', 'Anda tidak memiliki akses ke halaman ini.');
        header('Location: /TA2/index.php');
        exit;
    }
}
=======
// Redirect if not admin
function requireAdmin()
{
    if (!isAdmin()) {
        setFlash('auth', 'Anda tidak memiliki akses ke halaman ini', 'danger');
        header('Location: /index.php');
        exit;
    }
}
>>>>>>> ae7e33b89ac0a04007ceeceae2da2177530b51c0
