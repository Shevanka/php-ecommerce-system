<?php
/**
 * Proses Login
 * Menangani autentikasi pengguna
 */

require_once '../config/database.php';
require_once '../config/session.php';

try {
    // Validasi method request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method tidak diizinkan');
    }

    // Validasi dan ambil input
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validasi input
    if (empty($email) || empty($password)) {
        throw new Exception('Email dan password harus diisi');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format email tidak valid');
    }

    // Query user berdasarkan email
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Preparasi query gagal: ' . $conn->error);
    }

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Periksa apakah user ditemukan
    if ($result->num_rows === 0) {
        throw new Exception('Email atau password salah');
    }

    $user = $result->fetch_assoc();

    // Verifikasi password
    if (!password_verify($password, $user['password'])) {
        throw new Exception('Email atau password salah');
    }

    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'] ?? 'user';

    // Remember me functionality
    if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
        $rememberToken = bin2hex(random_bytes(32));
        
        // Simpan token ke database
        $tokenQuery = "UPDATE users SET remember_token = ? WHERE id = ?";
        $tokenStmt = $conn->prepare($tokenQuery);
        $tokenStmt->bind_param('si', $rememberToken, $user['id']);
        $tokenStmt->execute();

        // Set cookie
        setcookie('remember_user', $rememberToken, time() + (86400 * 7), '/');
    }

    // Flash message
    setFlash('login', 'Login berhasil! Selamat datang ' . $user['username'], 'success');

    // Redirect sesuai role
    if ($user['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../index.php');
    }
    exit;

} catch (Exception $e) {
    // Log error dan set flash message
    error_log('Login Error: ' . $e->getMessage());
    setFlash('login', $e->getMessage(), 'danger');
    header('Location: ../login.php');
    exit;
}
