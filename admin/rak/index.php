<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
$pdo = require __DIR__ . '/../../config/database.php';

wajibLogin();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$stmt = $pdo->query(
    "SELECT id_rak, kode_rak, nama_rak
     FROM lokasi_rak
     ORDER BY kode_rak"
);

$rak = $stmt->fetchAll();
$namaAdmin = $_SESSION['nama'] ?? 'Administrator';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Rak Buku - FINDLIB</title>

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

        .sidebar nav a {
            display: block;
            padding: 13px 15px;
            margin-bottom: 8px;
            border-radius: 7px;
            color: #e2e8f0;
            text-decoration: none;
            font-size: 14px;
            transition: .2s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: #31577f;
            color: white;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 40px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .page-header h1 {
            color: #17365d;
            font-size: 27px;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #64748b;
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 43px;
            padding: 0 18px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: .2s;
        }

        .btn-add {
            background: #24558a;
            color: white;
        }

        .btn-add:hover {
            background: #17365d;
        }

        .btn-back {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-back:hover {
            background: #cbd5e1;
        }

        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .05);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 22px;
        }

        .card-header h2 {
            color: #17365d;
            font-size: 19px;
            margin-bottom: 6px;
        }

        .card-header p {
            color: #94a3b8;
            font-size: 13px;
        }

        .total-badge {
            padding: 9px 14px;
            border-radius: 7px;
            background: #eaf1f8;
            color: #24558a;
            font-size: 13px;
            font-weight: bold;
            white-space: nowrap;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 650px;
            border-collapse: collapse;
        }

        thead {
            background: #f1f5f9;
        }

        th {
            padding: 16px 18px;
            color: #475569;
            font-size: 13px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        td {
            padding: 17px 18px;
            border-bottom: 1px solid #edf0f4;
            color: #475569;
            font-size: 14px;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .number-cell {
            width: 65px;
            color: #94a3b8;
        }

        .code-badge {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 6px;
            background: #eaf1f8;
            color: #24558a;
            font-size: 12px;
            font-weight: bold;
        }

        .action-cell {
            white-space: nowrap;
        }

        .btn-edit {
            display: inline-block;
            padding: 8px 13px;
            border-radius: 6px;
            background: #eaf1f8;
            color: #24558a;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-edit:hover {
            background: #d7e5f3;
        }

        .btn-delete {
            display: inline-block;
            padding: 8px 13px;
            border-radius: 6px;
            background: #fee2e2;
            color: #b91c1c;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-delete:hover {
            background: #fecaca;
        }

        .empty-state {
            padding: 45px 20px;
            text-align: center;
            color: #94a3b8;
        }

        .empty-state strong {
            display: block;
            margin-bottom: 8px;
            color: #475569;
            font-size: 16px;
        }

        @media (max-width: 850px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .sidebar nav {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 5px;
            }

            .main {
                padding: 25px 18px;
            }
        }

        @media (max-width: 550px) {
            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .header-actions {
                width: 100%;
            }

            .header-actions .btn {
                flex: 1;
            }

            .card {
                padding: 18px;
            }

            .card-header {
                align-items: flex-start;
                flex-direction: column;
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

        <nav>
            <a href="../dashboard.php">Dashboard</a>
            <a href="../buku/index.php">Kelola Buku</a>
            <a href="../kategori/index.php">Kategori Buku</a>
            <a href="index.php" class="active">Lokasi Rak</a>
            <a href="../akun/index.php">Kelola Akun</a>
            <a href="../riwayat-stok/index.php">Riwayat Stok</a>
        </nav>
    </aside>

    <main class="main">

        <header class="page-header">
            <div>
                <h1>Kelola Rak Buku</h1>
                <p>Daftar seluruh lokasi rak buku FINDLIB.</p>
            </div>

            <div class="header-actions">
                <a href="tambah.php" class="btn btn-add">
                    + Tambah Rak
                </a>

                <a href="../dashboard.php" class="btn btn-back">
                    Kembali
                </a>
            </div>
        </header>

        <section class="card">

            <div class="card-header">
                <div>
                    <h2>Daftar Lokasi Rak</h2>
                    <p>Informasi kode dan nama lokasi rak buku.</p>
                </div>

                <div class="total-badge">
                    Total Rak: <?= count($rak) ?>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode Rak</th>
                            <th>Nama Rak</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($rak)): ?>
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <strong>Belum Ada Data Rak</strong>
                                        Silakan tambahkan data rak buku terlebih dahulu.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rak as $index => $item): ?>
                                <tr>
                                    <td class="number-cell">
                                        <?= $index + 1 ?>
                                    </td>

                                    <td>
                                        <span class="code-badge">
                                            <?= htmlspecialchars($item['kode_rak'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($item['nama_rak'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td class="action-cell">
                                        <a
                                            href="edit.php?id=<?= (int) $item['id_rak'] ?>"
                                            class="btn-edit"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="hapus.php"
                                            method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Yakin ingin menghapus rak ini?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="id_rak"
                                                value="<?= (int) $item['id_rak'] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                                            >

                                            <button type="submit" class="btn-delete">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </section>

    </main>
</div>
</body>
</html>
