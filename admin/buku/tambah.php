<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

$kategori = $pdo->query(
    "SELECT id_kategori, nama_kategori
     FROM kategori ORDER BY nama_kategori"
)->fetchAll();

$rak = $pdo->query(
    "SELECT id_rak, kode_rak, nama_rak
     FROM lokasi_rak ORDER BY kode_rak"
)->fetchAll();

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
        $tahun = filter_var($_POST['tahun_terbit'] ?? '', FILTER_VALIDATE_INT);
        $id_kategori = filter_var($_POST['id_kategori'] ?? '', FILTER_VALIDATE_INT);
        $id_rak = filter_var($_POST['id_rak'] ?? '', FILTER_VALIDATE_INT);
        $stok = filter_var($_POST['jumlah_stok'] ?? '', FILTER_VALIDATE_INT);

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
            $error = 'Periksa kembali semua data yang dimasukkan.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO buku
                    (no_katalog, judul, penulis, penerbit,
                     tahun_terbit, id_kategori, id_rak, jumlah_stok)
                    VALUES
                    (:no_katalog, :judul, :penulis, :penerbit,
                     :tahun, :kategori, :rak, :stok)"
                );

                $stmt->execute([
                    'no_katalog' => $no_katalog,
                    'judul' => $judul,
                    'penulis' => $penulis,
                    'penerbit' => $penerbit,
                    'tahun' => $tahun,
                    'kategori' => $id_kategori,
                    'rak' => $id_rak,
                    'stok' => $stok
                ]);

                header('Location: /FindLib/admin/buku/index.php?status=berhasil');
                exit;

            } catch (PDOException $e) {
                error_log('Tambah buku gagal: ' . $e->getMessage());
                $error = 'Gagal menambahkan buku. Periksa nomor katalog atau data lainnya.';
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
    <title>Tambah Buku - FINDLIB</title>
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
        <h1>Tambah Buku</h1>
        <p class="form-description">
            Masukkan informasi buku baru ke dalam koleksi FINDLIB.
        </p>

        <?php if ($error !== ''): ?>
            <div class="error-message">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
            >

            <div class="form-group">
                <label for="no_katalog">Nomor Katalog</label>
                <input type="text" id="no_katalog" name="no_katalog"
                       value="<?= htmlspecialchars($_POST['no_katalog'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="judul">Judul Buku</label>
                <input type="text" id="judul" name="judul"
                       value="<?= htmlspecialchars($_POST['judul'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="penulis">Penulis</label>
                <input type="text" id="penulis" name="penulis"
                       value="<?= htmlspecialchars($_POST['penulis'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="penerbit">Penerbit</label>
                <input type="text" id="penerbit" name="penerbit"
                       value="<?= htmlspecialchars($_POST['penerbit'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="tahun_terbit">Tahun Terbit</label>
                <input type="number" id="tahun_terbit" name="tahun_terbit"
                       min="1000" max="<?= date('Y') ?>"
                       value="<?= htmlspecialchars($_POST['tahun_terbit'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="id_kategori">Kategori</label>
                <select id="id_kategori" name="id_kategori" required>
                    <option value="">-- Pilih Kategori --</option>

                    <?php foreach ($kategori as $item): ?>
                        <option
                            value="<?= (int) $item['id_kategori'] ?>"
                            <?= (string) ($_POST['id_kategori'] ?? '') === (string) $item['id_kategori'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($item['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
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
                            <?= (string) ($_POST['id_rak'] ?? '') === (string) $item['id_rak'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($item['kode_rak'] . ' - ' . $item['nama_rak'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah_stok">Jumlah Stok</label>
                <input type="number" id="jumlah_stok" name="jumlah_stok"
                       min="0"
                       value="<?= htmlspecialchars($_POST['jumlah_stok'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>

            <div class="form-actions">
                <button type="submit" class="save-button">
                    Simpan Buku
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
