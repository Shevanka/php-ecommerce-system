<?php
/**
 * Proses Register
 * Menangani pendaftaran pengguna baru
 */

require_once '../config/database.php';
require_once '../config/session.php';

try {
    // Validasi method request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method tidak diizinkan');
    }

    // Ambil dan validasi input
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validasi input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        throw new Exception('Semua field harus diisi');
    }

    if (strlen($username) < 3) {
        throw new Exception('Username minimal 3 karakter');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format email tidak valid');
    }

    if (strlen($password) < 6) {
        throw new Exception('Password minimal 6 karakter');
    }

    if ($password !== $confirm_password) {
        throw new Exception('Password dan konfirmasi password tidak cocok');
    }

    // Periksa apakah username sudah terdaftar
    $checkUsername = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($checkUsername);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('Username sudah terdaftar');
    }

    // Periksa apakah email sudah terdaftar
    $checkEmail = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmail);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('Email sudah terdaftar');
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert user ke database
    $query = "INSERT INTO users (username, email, password, role, created_at, updated_at) 
              VALUES (?, ?, ?, 'user', NOW(), NOW())";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Preparasi query gagal: ' . $conn->error);
    }

    $role = 'user';
    $stmt->bind_param('sss', $username, $email, $hashedPassword);
    
    if (!$stmt->execute()) {
        throw new Exception('Gagal mendaftarkan user: ' . $stmt->error);
    }

    // Set session untuk auto-login
    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = 'user';

    // Flash message
    setFlash('register', 'Registrasi berhasil! Selamat datang ' . $username, 'success');

    // Redirect ke homepage
    header('Location: ../index.php');
    exit;

} catch (Exception $e) {
    // Log error dan set flash message
    error_log('Register Error: ' . $e->getMessage());
    setFlash('register', $e->getMessage(), 'danger');
    header('Location: ../register.php');
    exit;
}
