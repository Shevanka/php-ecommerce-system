<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /TA2/register.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validation
if (strlen($username) < 3) {
    setFlash('danger', 'Username minimal 3 karakter.');
    header('Location: /TA2/register.php');
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('danger', 'Format email tidak valid.');
    header('Location: /TA2/register.php');
    exit;
}
if (strlen($password) < 6) {
    setFlash('danger', 'Password minimal 6 karakter.');
    header('Location: /TA2/register.php');
    exit;
}
if ($password !== $confirmPassword) {
    setFlash('danger', 'Konfirmasi password tidak cocok.');
    header('Location: /TA2/register.php');
    exit;
}

// Duplicate check
$stmt = $conn->prepare('SELECT id FROM users WHERE nama = ? OR email = ? LIMIT 1');
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    setFlash('danger', 'Username atau email sudah digunakan.');
    header('Location: /TA2/register.php');
    exit;
}
$stmt->close();

// Insert
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO users (nama, email, password) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $username, $email, $hash);

if (!$stmt->execute()) {
    setFlash('danger', 'Registrasi gagal, silakan coba lagi.');
    header('Location: /TA2/register.php');
    exit;
}

setFlash('success', 'Registrasi berhasil!');
header('Location: /TA2/login.php');
exit;
