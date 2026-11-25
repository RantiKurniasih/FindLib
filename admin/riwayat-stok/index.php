<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

$admin = requireAdmin($pdo);

$keyword = $_GET['keyword'] ?? '';
$keyword = is_string($keyword) ? trim($keyword) : '';

$error = null;
$riwayat = [];

try {
    $sql = '
        SELECT
            rs.id_riwayat,
            rs.stok_sebelum,
            rs.stok_sesudah,
            rs.waktu_perubahan,
            b.judul,
            p.nama AS nama_admin
        FROM riwayat_status rs
        INNER JOIN buku b
            ON rs.id_buku = b.id_buku
        INNER JOIN pengguna p
            ON rs.id_pengguna = p.id_pengguna
    ';

    if ($keyword !== '') {
        $sql .= '
            WHERE b.judul LIKE :keyword
            OR p.nama LIKE :keyword
        ';
    }

    $sql .= '
        ORDER BY rs.waktu_perubahan DESC,
                 rs.id_riwayat DESC
    ';

    $stmt = $pdo->prepare($sql);

    if ($keyword !== '') {
        $stmt->execute([
            'keyword' => '%' . $keyword . '%'
        ]);
    } else {
        $stmt->execute();
    }

    $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Riwayat stok error: ' . $e->getMessage());
    $error = 'Data riwayat stok gagal dimuat. Periksa koneksi database.';
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

    <title>Riwayat Stok - FINDLIB</title>

    <link rel="stylesheet" href="../../assets/css/admin.css">

    <style>
        .riwayat-page {
            margin: 0;
            padding: 30px;
            min-height: 100vh;
            background: #f4f7fb;
            color: #263445;
            font-family: Arial, Helvetica, sans-serif;
        }

        .riwayat-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .riwayat-header {
            margin-bottom: 25px;
        }

        .riwayat-header h1 {
            color: #17365d;
            margin: 0 0 10px;
            font-size: 27px;
        }

        .riwayat-header p {
            color: #64748b;
            margin: 0;
            line-height: 1.6;
        }

        .riwayat-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin: 25px 0;
        }

        .riwayat-button {
            display: inline-block;
            padding: 11px 17px;
            border-radius: 7px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            font-weight: 600;
        }

        .riwayat-back {
            background: #64748b;
        }

        .riwayat-back:hover {
            background: #475569;
        }

        .riwayat-search {
            background: white;
            padding: 22px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 22px;
        }

        .riwayat-search label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            color: #17365d;
        }

        .riwayat-search form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .riwayat-search input {
            flex: 1;
            min-width: 200px;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
        }

        .riwayat-search button {
            padding: 12px 20px;
            border: none;
            border-radius: 7px;
            background: #2563eb;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }

        .riwayat-search button:hover {
            background: #1d4ed8;
        }

        .riwayat-reset {
            display: inline-block;
            margin-top: 12px;
            color: #2563eb;
            text-decoration: none;
            font-size: 13px;
        }

        .riwayat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .riwayat-card-header {
            padding: 20px 22px;
            border-bottom: 1px solid #e2e8f0;
        }

        .riwayat-card-header h2 {
            margin: 0;
            color: #17365d;
            font-size: 19px;
        }

        .riwayat-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .riwayat-table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        .riwayat-table th,
        .riwayat-table td {
            padding: 15px 16px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            font-size: 14px;
        }

        .riwayat-table th {
            background: #17365d;
            color: white;
            font-weight: 600;
        }

        .riwayat-table tbody tr:hover {
            background: #f8fafc;
        }

        .stok-lama {
            display: inline-block;
            padding: 6px 10px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 6px;
            font-weight: 600;
        }

        .stok-baru {
            display: inline-block;
            padding: 6px 10px;
            background: #dcfce7;
            color: #166534;
            border-radius: 6px;
            font-weight: 600;
        }

        .riwayat-empty {
            padding: 35px !important;
            text-align: center !important;
            color: #64748b;
        }

        .riwayat-error {
            padding: 15px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .riwayat-count {
            color: #64748b;
            font-size: 13px;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .riwayat-page {
                padding: 18px;
            }

            .riwayat-header h1 {
                font-size: 23px;
            }

            .riwayat-search {
                padding: 16px;
            }

            .riwayat-search form {
                flex-direction: column;
            }

            .riwayat-search input,
            .riwayat-search button {
                width: 100%;
            }
        }
    </style>
</head>

<body class="riwayat-page">

    <main class="riwayat-container">

        <header class="riwayat-header">
            <h1>Riwayat Perubahan Stok</h1>
            <p>
                Halo, <?= h($admin['nama']) ?>.
                Berikut catatan perubahan stok buku FINDLIB.
            </p>
        </header>

        <div class="riwayat-actions">
            <a href="../dashboard.php"
               class="riwayat-button riwayat-back">
                ← Kembali ke Dashboard
            </a>
        </div>

        <section class="riwayat-search">
            <label for="keyword">Cari Riwayat</label>

            <form method="GET" action="index.php">
                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    placeholder="Cari judul buku atau nama admin..."
                    value="<?= h($keyword) ?>"
                >

                <button type="submit">Cari</button>
            </form>

            <a href="index.php" class="riwayat-reset">
                Reset pencarian
            </a>
        </section>

        <?php if ($error !== null): ?>

            <div class="riwayat-error">
                <?= h($error) ?>
            </div>

        <?php else: ?>

            <section class="riwayat-card">

                <div class="riwayat-card-header">
                    <h2>Daftar Riwayat Stok Buku</h2>
                </div>

                <div class="riwayat-table-wrapper">
                    <table class="riwayat-table">

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Judul Buku</th>
                                <th>Admin</th>
                                <th>Stok Sebelum</th>
                                <th>Stok Sesudah</th>
                                <th>Waktu Perubahan</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (empty($riwayat)): ?>

                                <tr>
                                    <td colspan="6" class="riwayat-empty">
                                        Belum ada riwayat perubahan stok.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($riwayat as $i => $item): ?>

                                    <tr>
                                        <td><?= $i + 1 ?></td>

                                        <td>
                                            <strong>
                                                <?= h($item['judul']) ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= h($item['nama_admin']) ?>
                                        </td>

                                        <td>
                                            <span class="stok-lama">
                                                <?= (int) $item['stok_sebelum'] ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="stok-baru">
                                                <?= (int) $item['stok_sesudah'] ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= h($item['waktu_perubahan']) ?>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>
                </div>

            </section>

            <p class="riwayat-count">
                Total riwayat ditemukan: <?= count($riwayat) ?> data
            </p>

        <?php endif; ?>

    </main>

</body>
</html>
