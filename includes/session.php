<?php
declare(strict_types=1);

// Konfigurasi keamanan session
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

$https = isset($_SERVER['HTTPS'])
    && $_SERVER['HTTPS'] !== 'off';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Memulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}