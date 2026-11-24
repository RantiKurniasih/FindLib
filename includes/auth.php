<?php
declare(strict_types=1);

require_once __DIR__ . '/session.php';

/**
 * Memeriksa apakah admin sudah login.
 */
function sudahLogin(): bool
{
    return isset($_SESSION['id_pengguna'])
        && isset($_SESSION['username']);
}

/**
 * Memeriksa hak akses admin.
 * Jika belum login, arahkan ke halaman login.
 */
function wajibLogin(): void
{
    if (!sudahLogin()) {
        header('Location: /FindLib/login.php');
        exit;
    }
}

/**
 * Mengambil ID pengguna yang sedang login.
 */
function idPenggunaLogin(): ?int
{
    return sudahLogin()
        ? (int) $_SESSION['id_pengguna']
        : null;
}

/**
 * Mengambil username pengguna yang sedang login.
 */
function usernameLogin(): ?string
{
    return sudahLogin()
        ? (string) $_SESSION['username']
        : null;
}

/**
 * Mengakhiri session pengguna.
 */
function logoutPengguna(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax'
            ]
        );
    }

    session_destroy();
}
