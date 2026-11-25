<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

$admin = requireAdmin($pdo);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID akun tidak valid.');
}

$stmt = $pdo->prepare(
    'SELECT id_pengguna, nama, username, email
     FROM pengguna
     WHERE id_pengguna = :id'
);
$stmt->execute(['id' => $id]);

$account = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$account) {
    http_response_code(404);
    exit('Akun tidak ditemukan.');
}

$error = '';
$nama = $account['nama'];
$username = $account['username'];
$email = $account['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim((string) ($_POST['nama'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));

    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'Token keamanan tidak valid.';
    } elseif ($nama === '' || $username === '' || $email === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (
        mb_strlen($nama) > 100 ||
        mb_strlen($username) > 50 ||
        strlen($email) > 150
    ) {
        $error = 'Data terlalu panjang.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $check = $pdo->prepare(
            'SELECT COUNT(*)
             FROM pengguna
             WHERE (username = :username OR email = :email)
               AND id_pengguna <> :id'
        );

        $check->execute([
            'username' => $username,
            'email' => $email,
            'id' => $id
        ]);

        if ((int) $check->fetchColumn() > 0) {
            $error = 'Username atau email sudah digunakan.';
        } else {
            try {
                $update = $pdo->prepare(
                    'UPDATE pengguna
                     SET nama = :nama,
                         username = :username,
                         email = :email
                     WHERE id_pengguna = :id'
                );

                $update->execute([
                    'nama' => $nama,
                    'username' => $username,
                    'email' => $email,
                    'id' => $id
                ]);

                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $error = 'Username atau email sudah digunakan.';
                } else {
                    error_log($e->getMessage());
                    $error = 'Gagal memperbarui akun.';
                }
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
    <title>Edit Akun - FINDLIB</title>

    <link rel="stylesheet" href="../../assets/css/admin.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f6fb;
            font-family: Arial, sans-serif;
            color: #263449;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            flex-shrink: 0;
            background: #17365d;
            color: white;
            padding: 28px 18px;
        }

        .brand {
            padding: 0 12px 28px;
            border-bottom: 1px solid rgba(255,255,255,.15);
            margin-bottom: 25px;
        }

        .brand h2 {
            margin: 0 0 6px;
            font-size: 25px;
            letter-spacing: 1px;
        }

        .brand p {
            margin: 0;
            font-size: 12px;
            color: #cbd8e8;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .sidebar-menu a {
            display: block;
            padding: 13px 14px;
            border-radius: 8px;
            color: #e5edf7;
            text-decoration: none;
            font-size: 14px;
            transition: .2s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #2c527e;
            color: white;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            padding: 38px;
        }

        .page-heading {
            margin-bottom: 25px;
        }

        .page-heading h1 {
            margin: 0 0 8px;
            color: #17365d;
            font-size: 27px;
        }

        .page-heading p {
            margin: 0;
            color: #718096;
            font-size: 14px;
        }

        .form-card {
            max-width: 760px;
            background: white;
            border-radius: 13px;
            padding: 30px;
            box-shadow: 0 4px 18px rgba(30, 55, 90, .07);
        }

        .form-card h3 {
            margin: 0 0 25px;
            padding-bottom: 17px;
            border-bottom: 1px solid #e8edf3;
            color: #17365d;
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 21px;
        }

        .form-group label {
            display: block;
            margin-bottom: 9px;
            color: #34445a;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border: 1px solid #d6deea;
            border-radius: 8px;
            background: #fff;
            color: #263449;
            font-size: 14px;
            outline: none;
            transition: .2s;
        }

        .form-group input:focus {
            border-color: #2c6fbb;
            box-shadow: 0 0 0 3px rgba(44, 111, 187, .12);
        }

        .form-hint {
            margin: 8px 0 0;
            color: #8290a3;
            font-size: 12px;
        }

        .alert-error {
            margin-bottom: 22px;
            padding: 13px 15px;
            border: 1px solid #f2c5c5;
            border-radius: 8px;
            background: #fff0f0;
            color: #b42318;
            font-size: 14px;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
            padding-top: 22px;
            border-top: 1px solid #e8edf3;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 43px;
            padding: 0 20px;
            border: 0;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-primary {
            background: #17365d;
            color: white;
        }

        .btn-primary:hover {
            background: #244d7c;
        }

        .btn-secondary {
            background: #e9eef5;
            color: #34445a;
        }

        .btn-secondary:hover {
            background: #dce5ef;
        }

        @media (max-width: 768px) {
            .admin-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 18px;
            }

            .brand {
                padding-bottom: 15px;
                margin-bottom: 15px;
            }

            .sidebar-menu {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .main-content {
                padding: 22px 16px;
            }

            .form-card {
                padding: 22px 18px;
            }
        }
    </style>
</head>
<body>

<div class="admin-layout">

    <aside class="sidebar">
        <div class="brand">
            <h2>FINDLIB</h2>
            <p>Admin Panel</p>
        </div>

        <nav class="sidebar-menu">
            <a href="../dashboard.php">Dashboard</a>
            <a href="../buku/index.php">Kelola Buku</a>
            <a href="../kategori/index.php">Kelola Kategori</a>
            <a href="../rak/index.php">Lokasi Rak</a>
            <a href="index.php" class="active">Kelola Akun</a>
            <a href="../riwayat-stok/index.php">Riwayat Stok</a>
        </nav>
    </aside>

    <main class="main-content">

        <div class="page-heading">
            <h1>Edit Akun Pengelola</h1>
            <p>Perbarui informasi akun administrator FINDLIB.</p>
        </div>

        <section class="form-card">
            <h3>Informasi Akun</h3>

            <?php if ($error !== ''): ?>
                <div class="alert-error" role="alert">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrfToken()) ?>"
                >

                <div class="form-group">
                    <label for="nama">Nama lengkap</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="<?= e($nama) ?>"
                        maxlength="100"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= e($username) ?>"
                        maxlength="50"
                        required
                    >
                    <p class="form-hint">
                        Username digunakan untuk masuk ke halaman admin.
                    </p>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= e($email) ?>"
                        maxlength="150"
                        required
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                    <a href="index.php" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </form>
        </section>

    </main>
</div>

</body>
</html>
