<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

// Logout hanya boleh melalui POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}

// Validasi CSRF
$csrf = $_POST['csrf_token'] ?? '';

if (
    !is_string($csrf) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $csrf)
) {
    http_response_code(403);
    exit('Permintaan tidak valid.');
}

// Hapus session pengguna
logoutPengguna();

// Kembali ke halaman login
header('Location: /FindLib/login.php');
exit;
