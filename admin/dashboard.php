<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$admin = requireAdmin($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - FINDLIB</title>

    <link
        rel="stylesheet"
        href="/FindLib/assets/css/admin.css"
    >
</head>
<body class="admin-dashboard-page">

    <div class="admin-layout">

        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <h2>📚 FINDLIB</h2>
                <p>Panel Administrator</p>
            </div>

            <nav aria-label="Navigasi admin">

                <a href="/FindLib/admin/dashboard.php"
                   class="sidebar-link active"
                   aria-current="page">
                    Dashboard
                </a>

                <a href="/FindLib/admin/buku/index.php"
                   class="sidebar-link">
                    Data Buku
                </a>

                <a href="/FindLib/admin/kategori/index.php"
                   class="sidebar-link">
                    Kategori Buku
                </a>

                <a href="/FindLib/admin/rak/index.php"
                   class="sidebar-link">
                    Lokasi Rak
                </a>

                <a href="/FindLib/admin/akun/index.php"
                   class="sidebar-link">
                    Akun Admin
                </a>

                <span class="sidebar-link disabled">
                    Riwayat Stok
                </span>

            </nav>
        </aside>

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

                    <button
                        type="submit"
                        class="admin-logout-button"
                    >
                        Logout
                    </button>
                </form>
            </header>

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

            <section
                class="admin-statistics"
                aria-labelledby="statistics-title"
            >
                <h2 id="statistics-title">
                    Ringkasan Koleksi Buku
                </h2>

                <div class="admin-placeholder">
                    <h3>Statistik Buku</h3>

                    <p>
                        Ringkasan jumlah judul buku,
                        total eksemplar, buku tersedia,
                        dan buku tidak tersedia akan
                        ditampilkan setelah fitur statistik
                        backend selesai diintegrasikan.
                    </p>
                </div>
            </section>

        </main>

    </div>

</body>
</html>
