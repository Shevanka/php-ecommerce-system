<?php

// Get the requested URI path
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedUrl = parse_url($uri);
$path = $parsedUrl['path'] ?? '/';

// Define the root of the project (one level up from /api)
$projectRoot = realpath(__DIR__ . '/..');

// Remove leading slash
$cleanPath = ltrim($path, '/');

// Handle root path
if ($cleanPath === '') {
    $targetFile = 'index.php';
} else {
    $targetFile = $cleanPath;
}

// Check if the target is a directory
$fullPath = $projectRoot . '/' . $targetFile;
if (is_dir($fullPath)) {
    // If it's the admin folder, map to dashboard.php
    if ($targetFile === 'admin' || $targetFile === 'admin/') {
        $targetFile = 'admin/dashboard.php';
    } else {
        // Fallback for other directories, check for index.php
        $targetFile = rtrim($targetFile, '/') . '/index.php';
    }
}

// Support extensionless URLs (e.g., /login -> /login.php)
if (!file_exists($projectRoot . '/' . $targetFile) && pathinfo($targetFile, PATHINFO_EXTENSION) === '') {
    if (file_exists($projectRoot . '/' . $targetFile . '.php')) {
        $targetFile .= '.php';
    }
}

// Resolve the absolute path
$absoluteTargetFile = realpath($projectRoot . '/' . $targetFile);

// Security Check: Ensure the file is inside the project root and exists
if ($absoluteTargetFile && strpos($absoluteTargetFile, $projectRoot) === 0 && is_file($absoluteTargetFile)) {
    // Format relative path for SCRIPT_NAME / PHP_SELF (replace windows backslashes)
    $relativePath = str_replace([$projectRoot, '\\'], ['', '/'], $absoluteTargetFile);
    $scriptName = '/' . ltrim($relativePath, '/');

    // Set appropriate $_SERVER variables so the script knows its rewritten URL
    $_SERVER['SCRIPT_FILENAME'] = $absoluteTargetFile;
    $_SERVER['SCRIPT_NAME'] = $scriptName;
    $_SERVER['PHP_SELF'] = $scriptName;

    // Change directory to the target file's directory so relative includes/requires work properly
    chdir(dirname($absoluteTargetFile));

    // Execute the PHP file
    require $absoluteTargetFile;
    exit;
}

// If the file doesn't exist, return 404
http_response_code(404);
if (file_exists($projectRoot . '/error.php')) {
    include_once $projectRoot . '/error.php';
} else {
    echo "<h1>404 Not Found</h1>";
    echo "<p>Halaman yang Anda cari tidak ditemukan.</p>";
}
