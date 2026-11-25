<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    "SELECT id_kategori, nama_kategori
     FROM kategori
     WHERE id_kategori = :id"
);
$stmt->execute([':id' => $id]);

$kategori = $stmt->fetch();

if (!$kategori) {
    header('Location: index.php');
    exit;
}

$error = '';
$nama_kategori = $kategori['nama_kategori'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        $error = 'Permintaan tidak valid. Muat ulang halaman.';
    } elseif ($nama_kategori === '') {
        $error = 'Nama kategori wajib diisi.';
    } else {
        try {
            $update = $pdo->prepare(
                "UPDATE kategori
                 SET nama_kategori = :nama
                 WHERE id_kategori = :id"
            );

            $update->execute([
                ':nama' => $nama_kategori,
                ':id' => $id
            ]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'Nama kategori sudah digunakan.';
            } else {
                error_log($e->getMessage());
                $error = 'Terjadi kesalahan saat mengubah kategori.';
            }
        }
    }
}

function h(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori - FINDLIB</title>

    <link rel="stylesheet" href="/FindLib/assets/css/style.css">

    <style>
        .form-container {
            max-width: 650px;
            margin: 45px auto;
            padding: 20px;
        }

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }

        .form-card h1 {
            color: #173b70;
            margin-bottom: 10px;
        }

        .form-description {
            color: #666;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #263b59;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd5e0;
            border-radius: 7px;
            box-sizing: border-box;
            font-size: 15px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2457a7;
            box-shadow: 0 0 0 2px rgba(36,87,167,0.12);
        }

        .error-message {
            background: #fde4e4;
            color: #a52222;
            padding: 13px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .save-button,
        .cancel-button {
            display: inline-block;
            padding: 12px 22px;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
        }

        .save-button {
            background: #2457a7;
            color: white;
        }

        .save-button:hover {
            background: #173b70;
        }

        .cancel-button {
            background: #e8edf4;
            color: #263b59;
        }

        .cancel-button:hover {
            background: #d5deea;
        }

        @media (max-width: 600px) {
            .form-container {
                margin: 20px auto;
                padding: 15px;
            }

            .form-card {
                padding: 20px;
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

<main class="form-container">

    <section class="form-card">
        <h1>Edit Kategori</h1>

        <p class="form-description">
            Perbarui nama kategori buku FINDLIB.
        </p>

        <?php if ($error !== ''): ?>
            <div class="error-message">
                <?= h($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= h($_SESSION['csrf_token']) ?>"
            >

            <div class="form-group">
                <label for="nama_kategori">Nama Kategori</label>

                <input
                    type="text"
                    id="nama_kategori"
                    name="nama_kategori"
                    maxlength="100"
                    value="<?= h($nama_kategori) ?>"
                    placeholder="Masukkan nama kategori"
                    required
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="save-button">
                    Simpan Perubahan
                </button>

                <a href="index.php" class="cancel-button">
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
