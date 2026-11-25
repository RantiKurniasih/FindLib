<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$admin = requireAdmin($pdo);

// Mengambil statistik koleksi buku
$stmt = $pdo->query(
    "SELECT
        COUNT(*) AS total_judul,
        COALESCE(SUM(jumlah_stok), 0) AS total_eksemplar,
        COALESCE(SUM(CASE WHEN jumlah_stok > 0 THEN 1 ELSE 0 END), 0) AS buku_tersedia,
        COALESCE(SUM(CASE WHEN jumlah_stok = 0 THEN 1 ELSE 0 END), 0) AS buku_tidak_tersedia
     FROM buku"
);

$statistik = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - FINDLIB</title>

    <link rel="stylesheet" href="/FindLib/assets/css/admin.css">
    <style>
    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .stat-card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .stat-card h3 {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 12px;
    }

    .stat-card .stat-number {
        font-size: 30px;
        font-weight: bold;
        color: #19395f;
    }

    @media (max-width: 900px) {
        .statistics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 500px) {
        .statistics-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body class="admin-dashboard-page">

    <div class="admin-layout">

        <!-- SIDEBAR -->
        <aside class="admin-sidebar">

            <div class="sidebar-brand">
                <h2>📚 FINDLIB</h2>
                <p>Panel Administrator</p>
            </div>

            <nav class="admin-sidebar-nav" aria-label="Navigasi admin">

                <a href="/FindLib/admin/dashboard.php"
                   class="sidebar-link active"
                   aria-current="page">
                    <span>Dashboard</span>
                </a>

                <a href="/FindLib/admin/buku/index.php"
                   class="sidebar-link">
                    <span>Data Buku</span>
                </a>

                <a href="/FindLib/admin/kategori/index.php"
                   class="sidebar-link">
                    <span>Kategori Buku</span>
                </a>

                <a href="/FindLib/admin/rak/index.php"
                   class="sidebar-link">
                    <span>Lokasi Rak</span>
                </a>

                <a href="/FindLib/admin/akun/index.php"
                   class="sidebar-link">
                    <span>Akun Admin</span>
                </a>

                <a href="/FindLib/admin/riwayat-stok/index.php"
                   class="sidebar-link">
                    <span>Riwayat Stok</span>
                </a>

            </nav>

        </aside>

        <!-- KONTEN UTAMA -->
        <main class="admin-main">

            <header class="admin-topbar">

                <div>
                    <h1>Dashboard FINDLIB</h1>
                    <p>
                        Sistem Informasi Ketersediaan Buku
                        Ruang Baca FMIPA UHO
                    </p>
                </div>

                <form method="POST" action="logout.php">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrfToken()) ?>"
                    >

                    <button type="submit" class="admin-logout-button">
                        Logout
                    </button>
                </form>

            </header>

            <!-- SAMBUTAN ADMIN -->
            <section class="admin-welcome">
                <h2>
                    Selamat datang,
                    <?= e($admin['nama']) ?>!
                </h2>

                <p>
                    Anda berhasil login sebagai
                    pengelola FINDLIB.
                </p>
            </section>

            <!-- STATISTIK -->
            <section
                class="admin-statistics"
                aria-labelledby="statistics-title"
            >

                <h2 id="statistics-title">
                    Ringkasan Koleksi Buku
                </h2>

                <div class="statistics-grid">

                    <div class="stat-card">
                        <h3>📚 Jumlah Judul Buku</h3>
                        <div class="stat-number">
                            <?= (int) $statistik['total_judul'] ?>
                        </div>
                    </div>

                    <div class="stat-card">
                        <h3>📖 Total Eksemplar</h3>
                        <div class="stat-number">
                            <?= (int) $statistik['total_eksemplar'] ?>
                        </div>
                    </div>

                    <div class="stat-card">
                        <h3>✅ Buku Tersedia</h3>
                        <div class="stat-number">
                            <?= (int) $statistik['buku_tersedia'] ?>
                        </div>
                    </div>

                    <div class="stat-card">
                        <h3>❌ Buku Tidak Tersedia</h3>
                        <div class="stat-number">
                            <?= (int) $statistik['buku_tidak_tersedia'] ?>
                        </div>
                    </div>

                </div>

            </section>

        </main>

    </div>

</body>
</html>
