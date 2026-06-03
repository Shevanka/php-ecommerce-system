<?php
/**
 * Session Configuration
 * Menginisialisasi dan mengatur session untuk aplikasi
 */

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi session
ini_set('session.cookie_lifetime', 86400); // 24 jam
ini_set('session.gc_maxlifetime', 86400);  // 24 jam

// Flash message helper
function setFlash($key, $message, $type = 'success') {
    $_SESSION['flash'] = [
        'key' => $key,
        'message' => $message,
        'type' => $type // success, error, warning, info
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Check authentication
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('auth', 'Anda harus login terlebih dahulu', 'warning');
        header('Location: /login.php');
        exit;
    }
}

// Redirect if not admin
function requireAdmin() {
    if (!isAdmin()) {
        setFlash('auth', 'Anda tidak memiliki akses ke halaman ini', 'danger');
        header('Location: /index.php');
        exit;
    }
}
