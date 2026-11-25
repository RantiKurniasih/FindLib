<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

$admin = requireAdmin($pdo);

$error = '';
$nama = '';
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? null;

    $namaInput = $_POST['nama'] ?? '';
    $usernameInput = $_POST['username'] ?? '';
    $emailInput = $_POST['email'] ?? '';
    $passwordInput = $_POST['password'] ?? '';
    $konfirmasiInput = $_POST['konfirmasi'] ?? '';

    $nama = is_string($namaInput) ? trim($namaInput) : '';
    $username = is_string($usernameInput) ? trim($usernameInput) : '';
    $email = is_string($emailInput) ? trim($emailInput) : '';

    $password = is_string($passwordInput) ? $passwordInput : '';
    $konfirmasi = is_string($konfirmasiInput) ? $konfirmasiInput : '';

    if (!verifyCsrf(is_string($csrf) ? $csrf : null)) {
        $error = 'Token keamanan tidak valid. Silakan muat ulang halaman.';
    } elseif (
        $nama === '' ||
        $username === '' ||
        $email === '' ||
        $password === '' ||
        $konfirmasi === ''
    ) {
        $error = 'Semua kolom wajib diisi.';
    } elseif (
        strlen($nama) > 100 ||
        strlen($username) > 50 ||
        strlen($email) > 150
    ) {
        $error = 'Data yang dimasukkan terlalu panjang.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak sesuai.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) FROM pengguna
                 WHERE username = :username
                    OR email = :email'
            );

            $stmt->execute([
                'username' => $username,
                'email' => $email
            ]);

            if ((int) $stmt->fetchColumn() > 0) {
                $error = 'Username atau email sudah digunakan.';
            } else {
                $hash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $stmt = $pdo->prepare(
                    'INSERT INTO pengguna
                     (nama, username, email,
                      password_hash, status_aktif)
                     VALUES
                     (:nama, :username, :email,
                      :password_hash, 1)'
                );

                $stmt->execute([
                    'nama' => $nama,
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => $hash
                ]);

                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Tambah akun error: ' . $e->getMessage());

            if ($e->getCode() === '23000') {
                $error = 'Username atau email sudah digunakan.';
            } else {
                $error = 'Terjadi kesalahan sistem. Gagal menyimpan akun.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Tambah Akun - FINDLIB</title>

    <link rel="stylesheet"
          href="/FindLib/assets/css/style.css">

    <style>
        .account-form-wrapper {
            max-width: 720px;
            margin: 45px auto;
            padding: 20px;
        }

        .account-form-card {
            background: #ffffff;
            padding: 32px;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        }

        .account-form-card h1 {
            color: #183b63;
            font-size: 27px;
            margin-bottom: 10px;
        }

        .form-description {
            color: #64748b;
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .account-form-group {
            margin-bottom: 20px;
        }

        .account-form-group label {
            display: block;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .account-form-group input {
            width: 100%;
            box-sizing: border-box;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s;
        }

        .account-form-group input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-hint {
            display: block;
            color: #64748b;
            font-size: 12px;
            margin-top: 6px;
        }

        .account-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 22px;
        }

        .account-form-actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        .account-save-button,
        .account-cancel-button {
            display: inline-block;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
        }

        .account-save-button {
            background: #1d4ed8;
            color: #ffffff;
        }

        .account-save-button:hover {
            background: #1e40af;
        }

        .account-cancel-button {
            background: #e2e8f0;
            color: #334155;
        }

        .account-cancel-button:hover {
            background: #cbd5e1;
        }

        @media (max-width: 600px) {
            .account-form-wrapper {
                margin: 20px auto;
                padding: 12px;
            }

            .account-form-card {
                padding: 22px;
            }

            .account-form-actions {
                flex-direction: column;
            }

            .account-form-actions a,
            .account-form-actions button {
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>

<body>

<header>
    <nav class="navbar">
        <div class="logo">📚 FINDLIB Admin</div>

        <ul class="nav-menu">
            <li><a href="/FindLib/index.php">Beranda</a></li>
            <li><a href="/FindLib/katalog.php">Katalog Buku</a></li>
            <li><a href="/FindLib/admin/dashboard.php">Dashboard Admin</a></li>
        </ul>
    </nav>
</header>

<main class="account-form-wrapper">

    <section class="account-form-card">

        <h1>Tambah Akun Pengelola</h1>

        <p class="form-description">
            Tambahkan akun administrator baru untuk membantu
            mengelola sistem informasi perpustakaan FINDLIB.
        </p>

        <?php if ($error !== ''): ?>
            <div class="account-error" role="alert">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrfToken()) ?>"
            >

            <div class="account-form-group">
                <label for="nama">Nama Lengkap</label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="<?= e($nama) ?>"
                    maxlength="100"
                    placeholder="Masukkan nama lengkap"
                    autocomplete="name"
                    required
                >
            </div>

            <div class="account-form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?= e($username) ?>"
                    maxlength="50"
                    placeholder="Masukkan username"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="account-form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= e($email) ?>"
                    maxlength="150"
                    placeholder="contoh@email.com"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="account-form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    placeholder="Minimal 8 karakter"
                    autocomplete="new-password"
                    required
                >
                <small class="form-hint">
                    Password harus terdiri dari minimal 8 karakter.
                </small>
            </div>

            <div class="account-form-group">
                <label for="konfirmasi">
                    Konfirmasi Password
                </label>
                <input
                    type="password"
                    id="konfirmasi"
                    name="konfirmasi"
                    minlength="8"
                    placeholder="Masukkan kembali password"
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="account-form-actions">
                <button
                    type="submit"
                    class="account-save-button"
                >
                    Simpan Akun
                </button>

                <a
                    href="index.php"
                    class="account-cancel-button"
                >
                    Kembali
                </a>
            </div>

        </form>

    </section>

</main>

<footer>
    <p>
        &copy; <?= date('Y') ?> FINDLIB -
        Ruang Baca FMIPA Universitas Halu Oleo
    </p>
</footer>

</body>
</html>
