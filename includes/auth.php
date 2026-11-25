<?php
declare(strict_types=1);

const FINDLIB_BASE_URL = '/FindLib';

require_once __DIR__ . '/session.php';

/**
 * Mengamankan output HTML.
 */
function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

/**
 * Membuat token CSRF.
 */
function csrfToken(): string
{
    if (
        !isset($_SESSION['csrf_token']) ||
        !is_string($_SESSION['csrf_token']) ||
        $_SESSION['csrf_token'] === ''
    ) {
        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf_token'];
}

/**
 * Memeriksa token CSRF.
 */
function verifyCsrf(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Memeriksa status login.
 */
function sudahLogin(): bool
{
    return isset($_SESSION['admin_id'])
        && filter_var(
            $_SESSION['admin_id'],
            FILTER_VALIDATE_INT
        ) !== false
        && (int) $_SESSION['admin_id'] > 0;
}

/**
 * Mengambil ID admin yang sedang login.
 */
function idPenggunaLogin(): ?int
{
    return sudahLogin()
        ? (int) $_SESSION['admin_id']
        : null;
}

/**
 * Mengambil username admin.
 */
function usernameLogin(): ?string
{
    return isset($_SESSION['username'])
        && is_string($_SESSION['username'])
        ? $_SESSION['username']
        : null;
}

/**
 * Memastikan admin sudah login.
 */
function wajibLogin(): void
{
    if (!sudahLogin()) {
        header(
            'Location: ' .
            FINDLIB_BASE_URL .
            '/admin/login.php'
        );
        exit;
    }
}

/**
 * Memeriksa login dan status aktif admin melalui database.
 */
function requireAdmin(PDO $pdo): array
{
    if (!sudahLogin()) {
        header(
            'Location: ' .
            FINDLIB_BASE_URL .
            '/admin/login.php'
        );
        exit;
    }

    try {
        $stmt = $pdo->prepare(
            'SELECT id_pengguna, nama, username,
                    email, status_aktif
             FROM pengguna
             WHERE id_pengguna = :id
             LIMIT 1'
        );

        $stmt->execute([
            'id' => idPenggunaLogin()
        ]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log(
            'Admin authentication error: ' .
            $e->getMessage()
        );

        http_response_code(500);
        exit('Terjadi kesalahan sistem.');
    }

    if (
        !$admin ||
        (int) $admin['status_aktif'] !== 1
    ) {
        logoutPengguna();

        header(
            'Location: ' .
            FINDLIB_BASE_URL .
            '/admin/login.php'
        );
        exit;
    }

    return $admin;
}

/**
 * Mengakhiri session admin.
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