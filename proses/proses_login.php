<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    setFlash('danger', 'Username dan password wajib diisi.');
    header('Location: ../login.php');
    exit;
}

$stmt = $conn->prepare('SELECT id, nama, password, role FROM users WHERE nama = ? OR email = ? LIMIT 1');
$stmt->bind_param('ss', $username, $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    setFlash('danger', 'Username atau password salah.');
    header('Location: ../login.php');
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['nama'];
$_SESSION['user_name'] = $user['nama'];
$_SESSION['role'] = $user['role'];
session_regenerate_id(true);

header('Location: ' . ($user['role'] === 'admin'
    ? BASE_URL . '/admin/dashboard.php'
    : BASE_URL . '/index.php'));
exit;
