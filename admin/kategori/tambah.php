<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
$pdo = require __DIR__ . '/../../config/database.php';

wajibLogin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        $error = 'Permintaan tidak valid. Muat ulang halaman.';
    } else {
        $nama_kategori = trim($_POST['nama_kategori'] ?? '');

        if ($nama_kategori === '') {
            $error = 'Nama kategori wajib diisi.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO kategori (nama_kategori)
                     VALUES (:nama_kategori)"
                );

                $stmt->execute([
                    ':nama_kategori' => $nama_kategori
                ]);

                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $error = 'Nama kategori sudah tersedia.';
                } else {
                    error_log($e->getMessage());
                    $error = 'Terjadi kesalahan saat menyimpan kategori.';
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
    <title>Tambah Kategori - FINDLIB</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f6f9;
            font-family: Arial, sans-serif;
            color: #263445;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            min-width: 250px;
            background: #17365d;
            color: white;
            padding: 25px 18px;
        }

        .sidebar h2 {
            font-size: 21px;
            margin: 0 0 35px;
            padding: 0 10px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 13px 15px;
            margin-bottom: 9px;
            border-radius: 7px;
            font-size: 14px;
            transition: background .2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #294e79;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            padding: 35px;
        }

        .page-heading {
            margin-bottom: 25px;
        }

        .page-heading h1 {
            margin: 0 0 9px;
            font-size: 27px;
            color: #17365d;
        }

        .page-heading p {
            color: #718096;
            margin: 0;
        }

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,.05);
            max-width: 850px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #34445a;
        }

        .form-group input {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #d5dce5;
            border-radius: 7px;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #315f91;
            box-shadow: 0 0 0 3px rgba(49,95,145,.12);
        }

        .form-hint {
            font-size: 12px;
            color: #718096;
        }

        .error-message {
            background: #fff0f0;
            color: #b42318;
            padding: 13px 16px;
            border-radius: 7px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid #edf0f4;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border: none;
            border-radius: 7px;
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
            background: #244c78;
        }

        .btn-secondary {
            background: #e9edf2;
            color: #34445a;
        }

        .btn-secondary:hover {
            background: #dce3eb;
        }

        @media (max-width: 850px) {
            .sidebar {
                width: 210px;
                min-width: 210px;
            }

            .main-content {
                padding: 25px 18px;
            }
        }

        @media (max-width: 600px) {
            .admin-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-width: 0;
            }

            .sidebar h2 {
                margin-bottom: 18px;
            }

            .main-content {
                padding: 22px 15px;
            }

            .form-card {
                padding: 20px 16px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                text-align: center;
            }
        }
    </style>
</head>

<body>

<div class="admin-layout">

    <aside class="sidebar">
        <h2>📚 FINDLIB Admin</h2>

        <a href="../dashboard.php">Dashboard</a>
        <a href="../buku/index.php">Kelola Buku</a>
        <a href="index.php" class="active">Kelola Kategori</a>
        <a href="../rak/index.php">Kelola Rak</a>
        <a href="../akun/index.php">Kelola Akun</a>
        <a href="../riwayat-stok/index.php">Riwayat Stok</a>
        <a href="../logout.php">Logout</a>
    </aside>

    <main class="main-content">

        <div class="page-heading">
            <h1>Tambah Kategori</h1>
            <p>Tambahkan kategori baru untuk mengelompokkan koleksi buku FINDLIB.</p>
        </div>

        <div class="form-card">

            <?php if ($error !== ''): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                >

                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori</label>

                    <input
                        type="text"
                        id="nama_kategori"
                        name="nama_kategori"
                        maxlength="100"
                        value="<?= htmlspecialchars($_POST['nama_kategori'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Contoh: Matematika, Biologi, Fisika"
                        required
                    >

                    <span class="form-hint">
                        Masukkan nama kategori buku yang ingin ditambahkan.
                    </span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Simpan Kategori
                    </button>

                    <a href="index.php" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>
