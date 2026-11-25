<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

// Jika sudah login, arahkan ke dashboard admin
if (sudahLogin()) {
    header('Location: /FindLib/admin/dashboard.php');
    exit;
}

$error = '';

// Membuat token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';

    if (
        !is_string($csrf) ||
        !hash_equals($_SESSION['csrf_token'], $csrf)
    ) {
        $error = 'Permintaan tidak valid. Silakan muat ulang halaman.';
    } else {
        $identifier = trim($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($identifier === '' || !is_string($password) || $password === '') {
            $error = 'Username/email dan password wajib diisi.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "SELECT id_pengguna, nama, username,
                            password_hash, status_aktif
                     FROM pengguna
                     WHERE username = :username
                        OR email = :email
                     LIMIT 1"
                );

                $stmt->execute([
                    'username' => $identifier,
                    'email' => $identifier
                ]);

                $pengguna = $stmt->fetch();

                if (
                    $pengguna &&
                    (int) $pengguna['status_aktif'] === 1 &&
                    password_verify(
                        $password,
                        $pengguna['password_hash']
                    )
                ) {
                    session_regenerate_id(true);

                    $_SESSION['id_pengguna'] =
                        (int) $pengguna['id_pengguna'];

                    $_SESSION['nama'] =
                        $pengguna['nama'];

                    $_SESSION['username'] =
                        $pengguna['username'];

                    // Ganti token CSRF setelah login
                    $_SESSION['csrf_token'] =
                        bin2hex(random_bytes(32));

                    header('Location: /FindLib/admin/dashboard.php');
                    exit;
                }

                $error = 'Username/email atau password salah, atau akun tidak aktif.';

            } catch (PDOException $e) {
                error_log('Login error: ' . $e->getMessage());
                $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - FINDLIB</title>
</head>
<body>

    <h2>Login Admin FINDLIB</h2>

    <?php if ($error !== ''): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                $_SESSION['csrf_token'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <div>
            <label>Username atau Email</label>
            <input
                type="text"
                name="identifier"
                required
                autocomplete="username"
            >
        </div>

        <br>

        <div>
            <label>Password</label>
            <input
                type="password"
                name="password"
                required
                autocomplete="current-password"
            >
        </div>

        <br>

        <button type="submit">Login</button>
    </form>

</body>
</html>

