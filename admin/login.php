<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (sudahLogin()) {
    requireAdmin($pdo);
    header('Location: dashboard.php');
    exit;
}

$error = '';
$identity = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? null;
    $identityInput = $_POST['identity'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!is_string($csrf) || !verifyCsrf($csrf)) {
        $error = 'Sesi formulir tidak valid. Muat ulang halaman.';
    } elseif (!is_string($identityInput) || !is_string($password)) {
        $error = 'Data formulir tidak valid.';
    } else {
        $identity = trim($identityInput);

        if ($identity === '' || $password === '') {
            $error = 'Semua kolom wajib diisi.';
        } elseif (strlen($identity) > 150) {
            $error = 'Username atau email terlalu panjang.';
        } else {
            try {
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

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (
                    $user &&
                    (int) $user['status_aktif'] === 1 &&
                    password_verify($password, $user['password_hash'])
                ) {
                    session_regenerate_id(true);

                    $_SESSION['admin_id'] = (int) $user['id_pengguna'];
                    $_SESSION['nama'] = $user['nama'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    header('Location: dashboard.php');
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

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #0f172a, #123b70, #2563eb);
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
        }

        .login-card {
            background: #fff;
            padding: 42px 36px;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .25);
        }

        .brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-icon {
            width: 66px;
            height: 66px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            border-radius: 18px;
            background: #eaf2ff;
            font-size: 32px;
        }

        .brand h1 {
            color: #172554;
            font-size: 30px;
            letter-spacing: 2px;
            font-weight: 800;
        }

        .brand p {
            color: #64748b;
            font-size: 14px;
            margin-top: 8px;
        }

        .login-heading {
            margin-bottom: 25px;
        }

        .login-heading h2 {
            color: #1e293b;
            font-size: 22px;
            margin-bottom: 7px;
        }

        .login-heading p {
            color: #64748b;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 9px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
        }

        .form-group input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }

        .error-message {
            padding: 13px 15px;
            margin-bottom: 20px;
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 9px;
            font-size: 13px;
            line-height: 1.5;
        }

        .login-button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s;
        }

        .login-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .back-link {
            display: block;
            margin-top: 24px;
            text-align: center;
            color: #64748b;
            text-decoration: none;
            font-size: 13px;
        }

        .back-link:hover {
            color: #2563eb;
        }

        .footer {
            text-align: center;
            color: rgba(255, 255, 255, .75);
            font-size: 12px;
            margin-top: 22px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
            }
        }
    </style>
</head>

<body>

    <main class="login-wrapper">
        <section class="login-card">

            <div class="brand">
                <div class="brand-icon">📚</div>
                <h1>FINDLIB</h1>
                <p>Sistem Informasi Ketersediaan Buku</p>
            </div>

            <div class="login-heading">
                <h2>Login Administrator</h2>
                <p>Silakan masuk menggunakan akun administrator.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="error-message" role="alert">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrfToken()) ?>"
                >

                <div class="form-group">
                    <label for="identity">Username atau Email</label>
                    <input
                        type="text"
                        id="identity"
                        name="identity"
                        value="<?= e($identity) ?>"
                        maxlength="150"
                        placeholder="Masukkan username atau email"
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="login-button">
                    Masuk ke Dashboard
                </button>

            </form>

            <a href="../index.php" class="back-link">
                ← Kembali ke Halaman Utama
            </a>

        </section>

        <div class="footer">
            © 2026 FINDLIB. Sistem Informasi Ruang Baca.
        </div>
    </main>

</body>
</html>
