<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function h(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    "SELECT id_rak, kode_rak, nama_rak
     FROM lokasi_rak
     WHERE id_rak = :id"
);

$stmt->execute([':id' => $id]);
$rak = $stmt->fetch();

if (!$rak) {
    header('Location: index.php');
    exit;
}

$error = '';
$kode_rak = $rak['kode_rak'];
$nama_rak = $rak['nama_rak'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        $error = 'Permintaan tidak valid. Muat ulang halaman.';
    } else {
        $kode_rak = trim($_POST['kode_rak'] ?? '');
        $nama_rak = trim($_POST['nama_rak'] ?? '');

        if ($kode_rak === '' || $nama_rak === '') {
            $error = 'Semua kolom wajib diisi.';
        } else {
            try {
                $update = $pdo->prepare(
                    "UPDATE lokasi_rak
                     SET kode_rak = :kode,
                         nama_rak = :nama
                     WHERE id_rak = :id"
                );

                $update->execute([
                    ':kode' => $kode_rak,
                    ':nama' => $nama_rak,
                    ':id' => $id
                ]);

                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $error = 'Kode rak sudah digunakan.';
                } else {
                    error_log($e->getMessage());
                    $error = 'Terjadi kesalahan saat mengubah rak.';
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
    <title>Edit Rak - FINDLIB</title>
    <link rel="stylesheet" href="/FindLib/assets/css/style.css">

    <style>
        .form-container {
            max-width: 700px;
            margin: 45px auto;
            padding: 20px;
        }

        .form-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        }

        .form-card h1 {
            color: #1e293b;
            margin-bottom: 10px;
        }

        .form-description {
            color: #64748b;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            box-sizing: border-box;
            font-size: 15px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .save-button,
        .cancel-button {
            display: inline-block;
            padding: 11px 20px;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }

        .save-button {
            background: #2563eb;
            color: white;
        }

        .cancel-button {
            background: #e2e8f0;
            color: #334155;
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

    <div class="form-card">
        <h1>Edit Rak Buku</h1>

        <p class="form-description">
            Ubah informasi lokasi rak buku FINDLIB.
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
                <label for="kode_rak">Kode Rak</label>
                <input
                    type="text"
                    id="kode_rak"
                    name="kode_rak"
                    maxlength="50"
                    value="<?= h($kode_rak) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="nama_rak">Nama Rak</label>
                <input
                    type="text"
                    id="nama_rak"
                    name="nama_rak"
                    maxlength="100"
                    value="<?= h($nama_rak) ?>"
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
    </div>

</main>

<footer>
    <p>
        &copy; <?= date('Y') ?> FINDLIB -
        Ruang Baca FMIPA Universitas Halu Oleo
    </p>
</footer>

</body>
</html>
