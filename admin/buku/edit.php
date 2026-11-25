<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
$pdo = require __DIR__ . '/../../config/database.php';

wajibLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID buku tidak valid.');
}

$stmt = $pdo->prepare("SELECT * FROM buku WHERE id_buku = :id");
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
                $adminId = (int) ($_SESSION['admin_id'] ?? 0);

                if ($adminId < 1) {
                    throw new RuntimeException(
                        'Identitas admin tidak ditemukan.'
                    );
                }

                $pdo->beginTransaction();

                $stmt = $pdo->prepare(
                    "SELECT jumlah_stok
                     FROM buku
                     WHERE id_buku = :id
                     FOR UPDATE"
                );

                $stmt->execute(['id' => $id]);
                $dataStok = $stmt->fetch();

                if (!$dataStok) {
                    throw new RuntimeException(
                        'Data buku tidak ditemukan.'
                    );
                }

                $stokSebelum = (int) $dataStok['jumlah_stok'];
                $stokSesudah = (int) $stok;

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
                    'stok' => $stokSesudah,
                    'id' => $id
                ]);

                if ($stokSebelum !== $stokSesudah) {
                    $stmt = $pdo->prepare(
                        "INSERT INTO riwayat_status (
                            id_buku,
                            id_pengguna,
                            stok_sebelum,
                            stok_sesudah
                        ) VALUES (
                            :id_buku,
                            :id_pengguna,
                            :stok_sebelum,
                            :stok_sesudah
                        )"
                    );

                    $stmt->execute([
                        'id_buku' => $id,
                        'id_pengguna' => $adminId,
                        'stok_sebelum' => $stokSebelum,
                        'stok_sesudah' => $stokSesudah
                    ]);
                }

                $pdo->commit();

                header(
                    'Location: /FindLib/admin/buku/index.php?status=diubah'
                );
                exit;

            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                error_log(
                    'Edit buku atau pencatatan riwayat gagal: '
                    . $e->getMessage()
                );

                $error = 'Gagal memperbarui buku. Silakan periksa data dan coba kembali.';
            }
        }
    }
}

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Buku - FINDLIB</title>

    <link rel="stylesheet" href="../../assets/css/admin.css">

    <style>
        .edit-buku-layout {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 250px minmax(0, 1fr);
            background: #f4f7fb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .edit-buku-sidebar {
            background: #17365d;
            color: white;
            padding: 28px 18px;
        }

        .edit-buku-brand {
            padding: 0 10px 25px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }

        .edit-buku-brand h2 {
            margin: 0 0 5px;
            font-size: 25px;
            color: white;
        }

        .edit-buku-brand p {
            margin: 0;
            color: #cbd5e1;
            font-size: 12px;
        }

        .edit-buku-nav {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .edit-buku-nav a {
            display: flex;
            align-items: center;
            min-height: 46px;
            padding: 12px 16px;
            box-sizing: border-box;
            border-radius: 8px;
            color: #e2e8f0;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: .2s;
        }

        .edit-buku-nav a:hover {
            background: rgba(255,255,255,.12);
        }

        .edit-buku-nav a.active {
            background: #2563eb;
            color: white;
        }

        .edit-buku-main {
            min-width: 0;
            padding: 32px;
        }

        .edit-buku-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .edit-buku-topbar h1 {
            color: #17365d;
            font-size: 26px;
            margin: 0 0 7px;
        }

        .edit-buku-topbar p {
            color: #64748b;
            margin: 0;
            font-size: 14px;
        }

        .edit-buku-user {
            background: white;
            padding: 11px 16px;
            border-radius: 8px;
            color: #17365d;
            font-size: 14px;
            border: 1px solid #e2e8f0;
        }

        .edit-buku-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 28px;
            max-width: 950px;
            box-shadow: 0 3px 12px rgba(15,23,42,.04);
        }

        .edit-buku-card-title {
            margin-bottom: 25px;
            padding-bottom: 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        .edit-buku-card-title h2 {
            margin: 0 0 7px;
            color: #17365d;
            font-size: 20px;
        }

        .edit-buku-card-title p {
            margin: 0;
            color: #64748b;
            font-size: 13px;
        }

        .edit-buku-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .edit-buku-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .edit-buku-field.full {
            grid-column: 1 / -1;
        }

        .edit-buku-field label {
            color: #334155;
            font-size: 14px;
            font-weight: 600;
        }

        .edit-buku-field input,
        .edit-buku-field select {
            width: 100%;
            min-height: 44px;
            padding: 11px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: white;
            color: #334155;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            transition: .2s;
        }

        .edit-buku-field input:focus,
        .edit-buku-field select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }

        .edit-buku-field small {
            color: #94a3b8;
            font-size: 12px;
        }

        .edit-buku-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .edit-buku-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid #e2e8f0;
        }

        .edit-buku-buttons button,
        .edit-buku-buttons a {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 43px;
            padding: 11px 20px;
            border: none;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            box-sizing: border-box;
        }

        .edit-buku-save {
            background: #2563eb;
            color: white;
        }

        .edit-buku-save:hover {
            background: #1d4ed8;
        }

        .edit-buku-cancel {
            background: #e2e8f0;
            color: #334155;
        }

        .edit-buku-cancel:hover {
            background: #cbd5e1;
        }

        @media (max-width: 768px) {
            .edit-buku-layout {
                grid-template-columns: 1fr;
            }

            .edit-buku-sidebar {
                padding: 18px;
            }

            .edit-buku-brand {
                padding-bottom: 15px;
                margin-bottom: 15px;
            }

            .edit-buku-nav {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 8px;
            }

            .edit-buku-nav a {
                min-height: 42px;
                padding: 10px 12px;
            }

            .edit-buku-main {
                padding: 20px;
            }

            .edit-buku-form-grid {
                grid-template-columns: 1fr;
            }

            .edit-buku-field.full {
                grid-column: auto;
            }

            .edit-buku-card {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="edit-buku-layout">

    <aside class="edit-buku-sidebar">

        <div class="edit-buku-brand">
            <h2>FINDLIB</h2>
            <p>Admin Panel</p>
        </div>

        <nav class="edit-buku-nav">
            <a href="/FindLib/admin/dashboard.php">
                Dashboard
            </a>

            <a href="/FindLib/admin/buku/index.php" class="active">
                Kelola Buku
            </a>

            <a href="/FindLib/admin/kategori/index.php">
                Kelola Kategori
            </a>

            <a href="/FindLib/admin/rak/index.php">
                Kelola Rak
            </a>

            <a href="/FindLib/admin/akun/index.php">
                Kelola Akun
            </a>

            <a href="/FindLib/admin/riwayat-stok/index.php">
                Riwayat Stok
            </a>
        </nav>

    </aside>

    <main class="edit-buku-main">

        <header class="edit-buku-topbar">
            <div>
                <h1>Edit Buku</h1>
                <p>Perbarui informasi dan jumlah stok buku FINDLIB.</p>
            </div>

            <div class="edit-buku-user">
                Admin: <?= h($_SESSION['nama'] ?? 'Admin') ?>
            </div>
        </header>

        <section class="edit-buku-card">

            <div class="edit-buku-card-title">
                <h2>Formulir Edit Buku</h2>
                <p>Silakan periksa dan perbarui informasi buku berikut.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="edit-buku-error">
                    <?= h($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= h($_SESSION['csrf_token']) ?>"
                >

                <div class="edit-buku-form-grid">

                    <div class="edit-buku-field">
                        <label for="no_katalog">Nomor Katalog</label>
                        <input
                            type="text"
                            id="no_katalog"
                            name="no_katalog"
                            required
                            value="<?= h($buku['no_katalog']) ?>"
                        >
                    </div>

                    <div class="edit-buku-field">
                        <label for="judul">Judul Buku</label>
                        <input
                            type="text"
                            id="judul"
                            name="judul"
                            required
                            value="<?= h($buku['judul']) ?>"
                        >
                    </div>

                    <div class="edit-buku-field">
                        <label for="penulis">Penulis</label>
                        <input
                            type="text"
                            id="penulis"
                            name="penulis"
                            required
                            value="<?= h($buku['penulis']) ?>"
                        >
                    </div>

                    <div class="edit-buku-field">
                        <label for="penerbit">Penerbit</label>
                        <input
                            type="text"
                            id="penerbit"
                            name="penerbit"
                            required
                            value="<?= h($buku['penerbit']) ?>"
                        >
                    </div>

                    <div class="edit-buku-field">
                        <label for="tahun_terbit">Tahun Terbit</label>
                        <input
                            type="number"
                            id="tahun_terbit"
                            name="tahun_terbit"
                            min="1000"
                            max="<?= date('Y') ?>"
                            required
                            value="<?= (int) $buku['tahun_terbit'] ?>"
                        >
                    </div>

                    <div class="edit-buku-field">
                        <label for="jumlah_stok">Jumlah Stok</label>
                        <input
                            type="number"
                            id="jumlah_stok"
                            name="jumlah_stok"
                            min="0"
                            required
                            value="<?= (int) $buku['jumlah_stok'] ?>"
                        >
                        <small>Perubahan jumlah stok akan dicatat dalam riwayat.</small>
                    </div>

                    <div class="edit-buku-field">
                        <label for="id_kategori">Kategori Buku</label>
                        <select id="id_kategori" name="id_kategori" required>
                            <?php foreach ($kategori as $item): ?>
                                <option
                                    value="<?= (int) $item['id_kategori'] ?>"
                                    <?= (int) $buku['id_kategori'] === (int) $item['id_kategori'] ? 'selected' : '' ?>
                                >
                                    <?= h($item['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="edit-buku-field">
                        <label for="id_rak">Lokasi Rak</label>
                        <select id="id_rak" name="id_rak" required>
                            <?php foreach ($rak as $item): ?>
                                <option
                                    value="<?= (int) $item['id_rak'] ?>"
                                    <?= (int) $buku['id_rak'] === (int) $item['id_rak'] ? 'selected' : '' ?>
                                >
                                    <?= h($item['kode_rak'] . ' - ' . $item['nama_rak']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <div class="edit-buku-buttons">
                    <button type="submit" class="edit-buku-save">
                        Simpan Perubahan
                    </button>

                    <a
                        href="/FindLib/admin/buku/index.php"
                        class="edit-buku-cancel"
                    >
                        Kembali
                    </a>
                </div>

            </form>

        </section>

    </main>

</div>

</body>
</html>
