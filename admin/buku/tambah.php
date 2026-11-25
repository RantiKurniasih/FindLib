<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
$pdo = require __DIR__ . '/../../config/database.php';

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
            max-width: 950px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #34445a;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d5dce5;
            border-radius: 7px;
            font-size: 14px;
            background: #fff;
            color: #263445;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #315f91;
            box-shadow: 0 0 0 3px rgba(49,95,145,.12);
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
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
        <a href="index.php" class="active">Kelola Buku</a>
        <a href="../kategori/index.php">Kelola Kategori</a>
        <a href="../rak/index.php">Kelola Rak</a>
        <a href="../akun/index.php">Kelola Akun</a>
        <a href="../riwayat-stok/index.php">Riwayat Stok</a>
        <a href="../logout.php">Logout</a>
    </aside>

    <main class="main-content">

        <div class="page-heading">
            <h1>Tambah Buku</h1>
            <p>Tambahkan koleksi buku baru ke dalam katalog FINDLIB.</p>
        </div>

        <div class="form-card">

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

                <div class="form-grid">

                    <div class="form-group">
                        <label for="no_katalog">Nomor Katalog</label>
                        <input
                            type="text"
                            id="no_katalog"
                            name="no_katalog"
                            value="<?= htmlspecialchars($_POST['no_katalog'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Contoh: BK001"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="judul">Judul Buku</label>
                        <input
                            type="text"
                            id="judul"
                            name="judul"
                            value="<?= htmlspecialchars($_POST['judul'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Masukkan judul buku"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="penulis">Penulis</label>
                        <input
                            type="text"
                            id="penulis"
                            name="penulis"
                            value="<?= htmlspecialchars($_POST['penulis'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Masukkan nama penulis"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="penerbit">Penerbit</label>
                        <input
                            type="text"
                            id="penerbit"
                            name="penerbit"
                            value="<?= htmlspecialchars($_POST['penerbit'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Masukkan nama penerbit"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="tahun_terbit">Tahun Terbit</label>
                        <input
                            type="number"
                            id="tahun_terbit"
                            name="tahun_terbit"
                            min="1000"
                            max="<?= date('Y') ?>"
                            value="<?= htmlspecialchars($_POST['tahun_terbit'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Contoh: 2024"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="jumlah_stok">Jumlah Stok</label>
                        <input
                            type="number"
                            id="jumlah_stok"
                            name="jumlah_stok"
                            min="0"
                            value="<?= htmlspecialchars($_POST['jumlah_stok'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Masukkan jumlah stok"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="id_kategori">Kategori Buku</label>
                        <select id="id_kategori" name="id_kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategori as $item): ?>
                                <option
                                    value="<?= (int) $item['id_kategori'] ?>"
                                    <?= (string)($_POST['id_kategori'] ?? '') === (string)$item['id_kategori'] ? 'selected' : '' ?>
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
                                    <?= (string)($_POST['id_rak'] ?? '') === (string)$item['id_rak'] ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        $item['kode_rak'] . ' - ' . $item['nama_rak'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Simpan Buku
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
