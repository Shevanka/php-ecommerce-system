<?php

// ── Load Environment Variables (Dotenv) ───────────────────────
if (!defined('ENV_LOADED')) {
    define('ENV_LOADED', true);
    $envPath = __DIR__ . '/../.env';
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                if (preg_match('/^([\'"])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv("{$name}={$value}");
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
}

// ── Error Handling Configuration ──────────────────────────────
$isProduction = (getenv('APP_ENV') ?: 'development') === 'production';

if ($isProduction) {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
    ini_set('log_errors', '1');

    // Register exception handler
    set_exception_handler(function (Throwable $exception) {
        error_log('Uncaught Exception: ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine());
        error_log($exception->getTraceAsString());
        
        if (!headers_sent()) {
            $message = rawurlencode('Terjadi kesalahan internal pada server.');
            header("Location: " . (defined('BASE_URL') ? BASE_URL : '') . "/error.php?code=500&message={$message}");
            exit;
        }
        echo "Terjadi kesalahan internal pada server.";
        exit;
    });

    // Register shutdown handler for fatal errors
    register_shutdown_function(function () {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            error_log('Fatal Error: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);
            if (!headers_sent()) {
                $message = rawurlencode('Terjadi kesalahan fatal pada server.');
                header("Location: " . (defined('BASE_URL') ? BASE_URL : '') . "/error.php?code=500&message={$message}");
                exit;
            }
        }
    });
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// ── Session Security & Initialization ─────────────────────────
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_lifetime', '86400');
ini_set('session.gc_maxlifetime', '86400');

if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) || 
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
    ini_set('session.cookie_secure', '1');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = '';
    if (strpos($scriptName, '/admin/') !== false) {
        $base = substr($scriptName, 0, strpos($scriptName, '/admin/'));
    } else if (strpos($scriptName, '/proses/') !== false) {
        $base = substr($scriptName, 0, strpos($scriptName, '/proses/'));
    } else {
        $base = dirname($scriptName);
        if ($base === '/' || $base === '\\') {
            $base = '';
        }
    }
    define('BASE_URL', $base);
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
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        setFlash('danger', 'Anda tidak memiliki akses ke halaman ini.');
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}
