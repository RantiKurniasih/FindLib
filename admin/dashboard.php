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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - FINDLIB</title>
    <link rel="stylesheet" href="/FindLib/assets/css/style.css">

    <style>
        .admin-dashboard {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }

        .welcome-card {
            background: #f0f5ff;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 35px;
            border-left: 5px solid #2457a7;
        }

        .welcome-card h1 {
            color: #173b70;
            margin-bottom: 12px;
        }

        .admin-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .admin-menu a {
            display: block;
            padding: 25px;
            background: white;
            border-radius: 12px;
            text-decoration: none;
            color: #173b70;
            font-weight: bold;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            transition: 0.2s;
        }

        .admin-menu a:hover {
            transform: translateY(-4px);
            box-shadow: 0 7px 18px rgba(0,0,0,0.13);
        }

        .admin-menu span {
            display: block;
            font-size: 30px;
            margin-bottom: 12px;
        }

        .logout-form {
            margin-top: 35px;
            text-align: right;
        }

        .logout-form button {
            background: #c0392b;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
        }

        .logout-form button:hover {
            background: #962d22;
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">📚 FINDLIB</div>

        <ul class="nav-menu">
            <li><a href="/FindLib/index.php">Beranda</a></li>
            <li><a href="/FindLib/katalog.php">Katalog Buku</a></li>
            <li><a href="/FindLib/admin/dashboard.php">Dashboard</a></li>
        </ul>
    </nav>
</header>

<main class="admin-dashboard">

    <section class="welcome-card">
        <h1>Dashboard Admin FINDLIB</h1>

        <p>
            Selamat datang,
            <strong><?= e($admin['nama']) ?></strong>!
        </p>

        <p>Anda berhasil login sebagai pengelola FINDLIB.</p>
    </section>

    <section>
        <h2>Menu Pengelolaan</h2>

        <div class="admin-menu">

            <a href="buku/index.php">
                <span>📚</span>
                Kelola Data Buku
            </a>

            <a href="kategori/index.php">
                <span>📂</span>
                Kelola Kategori
            </a>

            <a href="rak/index.php">
                <span>🗄️</span>
                Kelola Rak Buku
            </a>

            <a href="akun/index.php">
                <span>👤</span>
                Kelola Akun Admin
            </a>

        </div>
    </section>

    <form method="POST" action="logout.php" class="logout-form">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrfToken()) ?>"
        >

        <button type="submit">Logout</button>
    </form>

</main>

<footer>
    <p>
        &copy; <?= date('Y') ?> FINDLIB -
        Ruang Baca FMIPA Universitas Halu Oleo
    </p>
</footer>

</body>
</html>
```