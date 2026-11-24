<?php
declare(strict_types=1);

const FINDLIB_BASE_URL = '/FindLib';

if (session_status() === PHP_SESSION_NONE) {
    $https = !empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off';

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => FINDLIB_BASE_URL,
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrf(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function requireAdmin(PDO $pdo): array
{
    if (empty($_SESSION['admin_id'])) {
        header(
            'Location: ' .
            FINDLIB_BASE_URL .
            '/admin/login.php'
        );
        exit;
    }

    $stmt = $pdo->prepare(
        'SELECT id_pengguna, nama, username,
                email, status_aktif
         FROM pengguna
         WHERE id_pengguna = :id
         LIMIT 1'
    );

    $stmt->execute([
        'id' => (int) $_SESSION['admin_id']
    ]);

    $admin = $stmt->fetch();

    if (!$admin || (int) $admin['status_aktif'] !== 1) {
        $_SESSION = [];
        session_regenerate_id(true);

        header(
            'Location: ' .
            FINDLIB_BASE_URL .
            '/admin/login.php'
        );
        exit;
    }

    return $admin;
}