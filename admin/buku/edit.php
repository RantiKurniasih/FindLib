<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID buku tidak valid.');
}

$stmt = $pdo->prepare(
    "SELECT * FROM buku WHERE id_buku = :id"
);
$stmt->execute(['id' => $id]);
$buku = $stmt->fetch();

if (!$buku) {
    http_response_code(404);
    exit('Data buku tidak ditemukan.');
}

$kategori = $pdo->query(
    "SELECT id_kategori, nama_kategori
     FROM kategori ORDER BY nama_kategori"
)->fetchAll();

$rak = $pdo->query(
    "SELECT id_rak, kode_rak, nama_rak
     FROM lokasi_rak ORDER BY kode_rak"
)->fetchAll();

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
        $no_katalog = trim($_POST['no_katalog'] ?? '');
        $judul = trim($_POST['judul'] ?? '');
        $penulis = trim($_POST['penulis'] ?? '');
        $penerbit = trim($_POST['penerbit'] ?? '');

        $tahun = filter_var(
            $_POST['tahun_terbit'] ?? '',
            FILTER_VALIDATE_INT
        );

        $id_kategori = filter_var(
            $_POST['id_kategori'] ?? '',
            FILTER_VALIDATE_INT
        );

        $id_rak = filter_var(
            $_POST['id_rak'] ?? '',
            FILTER_VALIDATE_INT
        );

        $stok = filter_var(
            $_POST['jumlah_stok'] ?? '',
            FILTER_VALIDATE_INT
        );

        if (
            $no_katalog === '' ||
            $judul === '' ||
            $penulis === '' ||
            $penerbit === '' ||
            $tahun === false ||
            $tahun < 1000 ||
            $tahun > (int) date('Y') ||
            $id_kategori === false ||
            $id_rak === false ||
            $stok === false ||
            $stok < 0
        ) {
            $error = 'Periksa kembali data yang dimasukkan.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "UPDATE buku SET
                        no_katalog = :no_katalog,
                        judul = :judul,
                        penulis = :penulis,
                        penerbit = :penerbit,
                        tahun_terbit = :tahun,
                        id_kategori = :kategori,
                        id_rak = :rak,
                        jumlah_stok = :stok
                     WHERE id_buku = :id"
                );

                $stmt->execute([
                    'no_katalog' => $no_katalog,
                    'judul' => $judul,
                    'penulis' => $penulis,
                    'penerbit' => $penerbit,
                    'tahun' => $tahun,
                    'kategori' => $id_kategori,
                    'rak' => $id_rak,
                    'stok' => $stok,
                    'id' => $id
                ]);

                header(
                    'Location: /FindLib/admin/buku/index.php?status=diubah'
                );
                exit;

            } catch (PDOException $e) {
                error_log('Edit buku gagal: ' . $e->getMessage());
                $error = 'Gagal memperbarui buku. Periksa nomor katalog dan data lainnya.';
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

$values = [
    'no_katalog' => $_POST['no_katalog'] ?? $buku['no_katalog'],
    'judul' => $_POST['judul'] ?? $buku['judul'],
    'penulis' => $_POST['penulis'] ?? $buku['penulis'],
    'penerbit' => $_POST['penerbit'] ?? $buku['penerbit'],
    'tahun_terbit' => $_POST['tahun_terbit'] ?? $buku['tahun_terbit'],
    'id_kategori' => $_POST['id_kategori'] ?? $buku['id_kategori'],
    'id_rak' => $_POST['id_rak'] ?? $buku['id_rak'],
    'jumlah_stok' => $_POST['jumlah_stok'] ?? $buku['jumlah_stok']
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - FINDLIB</title>
    <link rel="stylesheet" href="/FindLib/assets/css/style.css">

    <style>
        .form-container {
            max-width: 750px;
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
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #263b59;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd5e0;
            border-radius: 7px;
            box-sizing: border-box;
            font-size: 15px;
        }

        .form-group input:focus,
        .form-group select:focus {
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
        <h1>Edit Buku</h1>

        <p class="form-description">
            Perbarui informasi buku dalam koleksi FINDLIB.
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
                <label for="no_katalog">Nomor Katalog</label>
                <input type="text" id="no_katalog" name="no_katalog"
                       value="<?= h($values['no_katalog']) ?>" required>
            </div>

            <div class="form-group">
                <label for="judul">Judul Buku</label>
                <input type="text" id="judul" name="judul"
                       value="<?= h($values['judul']) ?>" required>
            </div>

            <div class="form-group">
                <label for="penulis">Penulis</label>
                <input type="text" id="penulis" name="penulis"
                       value="<?= h($values['penulis']) ?>" required>
            </div>

            <div class="form-group">
                <label for="penerbit">Penerbit</label>
                <input type="text" id="penerbit" name="penerbit"
                       value="<?= h($values['penerbit']) ?>" required>
            </div>

            <div class="form-group">
                <label for="tahun_terbit">Tahun Terbit</label>
                <input type="number" id="tahun_terbit" name="tahun_terbit"
                       min="1000" max="<?= date('Y') ?>"
                       value="<?= h($values['tahun_terbit']) ?>" required>
            </div>

            <div class="form-group">
                <label for="id_kategori">Kategori</label>
                <select id="id_kategori" name="id_kategori" required>
                    <option value="">-- Pilih Kategori --</option>

                    <?php foreach ($kategori as $item): ?>
                        <option
                            value="<?= (int) $item['id_kategori'] ?>"
                            <?= (string) $values['id_kategori'] === (string) $item['id_kategori']
                                ? 'selected'
                                : '' ?>
                        >
                            <?= h($item['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_rak">Lokasi Rak</label>
                <select id="id_rak" name="id_rak" required>
                    <option value="">-- Pilih Rak --</option>

                    <?php foreach ($rak as $item): ?>
                        <option
                            value="<?= (int) $item['id_rak'] ?>"
                            <?= (string) $values['id_rak'] === (string) $item['id_rak']
                                ? 'selected'
                                : '' ?>
                        >
                            <?= h($item['kode_rak'] . ' - ' . $item['nama_rak']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah_stok">Jumlah Stok</label>
                <input type="number" id="jumlah_stok" name="jumlah_stok"
                       min="0"
                       value="<?= h($values['jumlah_stok']) ?>" required>
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
