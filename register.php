<?php
session_start();

include 'config/database.php';

if (file_exists(__DIR__ . '/koneksi.php')) {
    include_once __DIR__ . '/koneksi.php';
}
if (file_exists(__DIR__ . '/config.php')) {
    include_once __DIR__ . '/config.php';
}

$mysqli = null;
if (isset($koneksi) && $koneksi instanceof mysqli) {
    $mysqli = $koneksi;
} elseif (isset($conn) && $conn instanceof mysqli) {
    $mysqli = $conn;
} else {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
}

if ($mysqli->connect_errno) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

$errors = [];
$success = false;
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($username === '') {
        $errors[] = 'Username is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('ss', $username, $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = 'Username or email already exists.';
            }
            $stmt->close();
        } else {
            $errors[] = 'Unable to check existing users.';
        }
    }

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        if ($stmt) {
            $stmt->bind_param('sss', $username, $email, $passwordHash);
            if ($stmt->execute()) {
                $success = true;
                $username = '';
                $email = '';
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
            $stmt->close();
        } else {
            $errors[] = 'Unable to save registration information.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 420px;
            margin: 60px auto;
            padding: 24px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-top: 0;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .button:hover {
            background: #0056b3;
        }

        .message {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 16px;
        }

        .message.error {
            background: #fdd;
            border: 1px solid #f99;
            color: #900;
        }

        .message.success {
            background: #dfd;
            border: 1px solid #9c9;
            color: #060;
        }

        .footer {
            margin-top: 16px;
            font-size: 14px;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Create an Account</h1>

        <?php if (!empty($errors)): ?>
            <div class="message error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success">
                Registration completed successfully. <a href="login.php">Click here to login</a>.
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>"
                    required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="button">Register</button>
        </form>

        <div class="footer">
            Already have an account? <a href="login.php">Login here</a>.
        </div>
    </div>
</body>

</html>