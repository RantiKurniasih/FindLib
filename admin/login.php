<?php
declare(strict_types=1);

// Koneksi database dan autentikasi dari backend Rangga
$pdo = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Admin yang sudah login diarahkan ke dashboard
if (!empty($_SESSION['admin_id'])) {
    requireAdmin($pdo);

    header('Location: dashboard.php');
    exit;
}

$error = '';
$identity = '';

// Proses login admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim(
        (string) ($_POST['identity'] ?? '')
    );

    $password = (string) ($_POST['password'] ?? '');
    $token = $_POST['csrf_token'] ?? null;

    if (!verifyCsrf($token)) {
        $error = 'Sesi formulir tidak valid. Muat ulang halaman.';
    } elseif ($identity === '' || $password === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (strlen($identity) > 150) {
        $error = 'Username atau email terlalu panjang.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id_pengguna, nama, username,
                    email, password_hash, status_aktif
             FROM pengguna
             WHERE username = :username
                OR email = :email
             LIMIT 1'
        );

        $stmt->execute([
            'username' => $identity,
            'email' => $identity
        ]);

        $user = $stmt->fetch();

        if (
            $user
            && (int) $user['status_aktif'] === 1
            && password_verify(
                $password,
                $user['password_hash']
            )
        ) {
            session_regenerate_id(true);

            $_SESSION['admin_id'] =
                (int) $user['id_pengguna'];

            unset($_SESSION['csrf_token']);

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Username/email atau password salah, atau akun tidak aktif.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login Admin - FINDLIB</title>

    <link
        rel="stylesheet"
        href="/FindLib/assets/css/admin.css"
    >
</head>

<body class="admin-login-page">

    <main class="login-container">

        <!-- Judul halaman login -->
        <header class="login-header">
            <h1>FINDLIB</h1>
            <h2>Login Admin</h2>
        </header>

        <!-- Pesan kesalahan login -->
        <?php if ($error !== ''): ?>
            <div class="login-error" role="alert">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <!-- Formulir login admin -->
        <form
            method="POST"
            action="login.php"
            class="login-form"
        >

            <!-- Token keamanan CSRF -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrfToken()) ?>"
            >

            <!-- Username atau Email -->
            <div class="login-field">
                <label for="identity">
                    Username atau Email
                </label>

                <input
                    type="text"
                    id="identity"
                    name="identity"
                    value="<?= e($identity) ?>"
                    maxlength="150"
                    autocomplete="username"
                    required
                >
            </div>

            <!-- Password -->
            <div class="login-field">
                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <!-- Tombol login -->
            <button
                type="submit"
                class="login-button"
            >
                Masuk
            </button>

        </form>

        <!-- Kembali ke halaman pengunjung -->
        <a
            href="/FindLib/index.php"
            class="login-back"
        >
            &larr; Kembali ke Beranda
        </a>

    </main>

</body>
</html>