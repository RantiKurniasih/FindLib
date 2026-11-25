<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
$pdo = require __DIR__ . '/../../config/database.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {
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

$kodeTampil = $_POST['kode_rak'] ?? $rak['kode_rak'];
$namaTampil = $_POST['nama_rak'] ?? $rak['nama_rak'];
$namaAdmin = $_SESSION['nama'] ?? 'Administrator';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rak Buku - FINDLIB</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5f9;
            color: #334155;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            padding: 30px 20px;
            background: #17365d;
            color: white;
            flex-shrink: 0;
        }

        .brand {
            font-size: 27px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .brand-desc {
            color: #cbd5e1;
            font-size: 13px;
            margin-bottom: 30px;
        }

        .admin-profile {
            padding: 13px;
            margin-bottom: 25px;
            background: rgba(255,255,255,.1);
            border-radius: 8px;
            font-size: 13px;
            line-height: 1.7;
        }

        .menu-title {
            color: #aebfd3;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 20px 0 12px;
        }

        .sidebar a {
            display: block;
            padding: 13px 15px;
            margin-bottom: 8px;
            border-radius: 7px;
            color: #e2e8f0;
            text-decoration: none;
            font-size: 14px;
            transition: .2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #31577f;
            color: white;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 40px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .topbar h1 {
            color: #17365d;
            font-size: 27px;
            margin-bottom: 8px;
        }

        .topbar p {
            color: #64748b;
            font-size: 14px;
        }

        .breadcrumb {
            color: #64748b;
            font-size: 13px;
        }

        .breadcrumb span {
            color: #17365d;
            font-weight: bold;
        }

        .card {
            max-width: 850px;
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
            border: 1px solid #e2e8f0;
        }

        .card-header {
            padding-bottom: 22px;
            margin-bottom: 25px;
            border-bottom: 1px solid #e2e8f0;
        }

        .card-header h2 {
            color: #17365d;
            font-size: 20px;
            margin-bottom: 8px;
        }

        .card-header p {
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 23px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #334155;
            font-size: 14px;
            font-weight: bold;
        }

        .form-group input {
            display: block;
            width: 100%;
            height: 48px;
            padding: 0 15px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #fff;
            color: #334155;
            font-size: 14px;
            outline: none;
            transition: .2s;
        }

        .form-group input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }

        .form-group small {
            display: block;
            color: #94a3b8;
            margin-top: 8px;
            font-size: 12px;
        }

        .alert {
            padding: 14px 16px;
            margin-bottom: 22px;
            border-radius: 7px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            font-size: 14px;
        }

        .form-footer {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            padding-top: 23px;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 45px;
            padding: 0 22px;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: .2s;
        }

        .btn-save {
            background: #24558a;
            color: white;
        }

        .btn-save:hover {
            background: #17365d;
        }

        .btn-back {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-back:hover {
            background: #cbd5e1;
        }

        @media (max-width: 800px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .main {
                padding: 25px 18px;
            }

            .topbar {
                display: block;
            }

            .breadcrumb {
                margin-top: 12px;
            }
        }

        @media (max-width: 480px) {
            .card {
                padding: 22px 18px;
            }

            .form-footer {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">FINDLIB</div>
        <div class="brand-desc">Sistem Informasi Perpustakaan</div>

        <div class="admin-profile">
            Login sebagai:<br>
            <strong><?= htmlspecialchars($namaAdmin, ENT_QUOTES, 'UTF-8') ?></strong>
        </div>

        <div class="menu-title">MENU UTAMA</div>

        <a href="../dashboard.php">Dashboard</a>
        <a href="../buku/index.php">Kelola Buku</a>
        <a href="../kategori/index.php">Kategori Buku</a>
        <a href="index.php" class="active">Lokasi Rak</a>
        <a href="../akun/index.php">Kelola Akun</a>
        <a href="../riwayat-stok/index.php">Riwayat Stok</a>
    </aside>

    <main class="main">
        <div class="topbar">
            <div>
                <h1>Edit Rak Buku</h1>
                <p>Perbarui informasi lokasi rak buku FINDLIB.</p>
            </div>

            <div class="breadcrumb">
                Dashboard / Lokasi Rak / <span>Edit Rak</span>
            </div>
        </div>

        <section class="card">
            <div class="card-header">
                <h2>Formulir Edit Rak</h2>
                <p>
                    Silakan perbarui kode dan nama rak pada kolom di bawah ini.
                    Pastikan informasi yang dimasukkan sudah benar.
                </p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert">
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
                    <label for="kode_rak">Kode Rak</label>
                    <input
                        type="text"
                        id="kode_rak"
                        name="kode_rak"
                        maxlength="50"
                        value="<?= htmlspecialchars((string) $kodeTampil, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Masukkan kode rak"
                        required
                    >
                    <small>Contoh: RAK-A01 atau RAK-B02.</small>
                </div>

                <div class="form-group">
                    <label for="nama_rak">Nama Rak</label>
                    <input
                        type="text"
                        id="nama_rak"
                        name="nama_rak"
                        maxlength="100"
                        value="<?= htmlspecialchars((string) $namaTampil, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Masukkan nama rak"
                        required
                    >
                    <small>Masukkan nama atau keterangan lokasi rak buku.</small>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-save">
                        Simpan Perubahan
                    </button>

                    <a href="index.php" class="btn btn-back">
                        Kembali
                    </a>
                </div>
            </form>
        </section>
    </main>

</div>
</body>
</html>
