<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FINDLIB - Ruang Baca FMIPA UHO</title>
    <link rel="stylesheet" href="/FindLib/assets/css/style.css">
</head>
<body>

    <header>
        <nav class="navbar">
            <div class="logo">📚 FINDLIB</div>

            <ul class="nav-menu">
                <li><a href="/FindLib/index.php">Beranda</a></li>
                <li><a href="/FindLib/katalog.php">Katalog Buku</a></li>
                <li><a href="/FindLib/admin/login.php">Login Admin</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Selamat Datang di FINDLIB</h1>

                <p>
                    Sistem Informasi Ketersediaan Buku
                    Ruang Baca FMIPA Universitas Halu Oleo
                </p>

                <form action="/FindLib/katalog.php" method="GET" class="search-box" role="search">
                    <label for="keyword">Cari Buku</label>

                    <div class="search-input">
                        <input
                            type="search"
                            id="keyword"
                            name="keyword"
                            placeholder="Cari judul atau penulis buku..."
                        >

                        <button type="submit">Cari Buku</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="features">
            <h2>Fitur FINDLIB</h2>

            <div class="feature-container">
                <article class="feature-card">
                    <h3>📖 Katalog Buku</h3>
                    <p>
                        Lihat daftar koleksi buku yang tersedia
                        di Ruang Baca FMIPA.
                    </p>
                </article>

                <article class="feature-card">
                    <h3>🔎 Pencarian Buku</h3>
                    <p>
                        Cari buku berdasarkan judul,
                        penulis, atau kategori.
                    </p>
                </article>

                <article class="feature-card">
                    <h3>📍 Lokasi dan Stok</h3>
                    <p>
                        Ketahui lokasi rak dan status
                        ketersediaan buku.
                    </p>
                </article>
            </div>
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