<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FINDLIB - Sistem Informasi Ketersediaan Buku Ruang Baca FMIPA Universitas Halu Oleo.">
    <title>FINDLIB - Ruang Baca FMIPA UHO</title>
    <link rel="stylesheet" href="/FindLib/assets/css/style.css">

    <style>
        .hero {
            padding: 80px 20px;
            text-align: center;
        }

        .hero-content {
            max-width: 850px;
            margin: auto;
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            margin-bottom: 18px;
        }

        .hero-content > p {
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .search-box {
            max-width: 650px;
            margin: 30px auto 0;
            text-align: left;
        }

        .search-box label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .search-input {
            display: flex;
            gap: 10px;
        }

        .search-input input {
            flex: 1;
            min-width: 0;
            padding: 13px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
        }

        .search-input button {
            padding: 13px 22px;
            border: none;
            border-radius: 8px;
            background: #1e40af;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }

        .search-input button:hover {
            background: #1e3a8a;
        }

        .features {
            padding: 55px 20px;
            text-align: center;
        }

        .features h2 {
            margin-bottom: 30px;
        }

        .feature-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            max-width: 1100px;
            margin: auto;
        }

        .feature-card {
            padding: 28px 22px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.08);
        }

        .feature-card h3 {
            margin-bottom: 14px;
        }

        .feature-card p {
            line-height: 1.7;
            color: #475569;
        }

        @media (max-width: 700px) {
            .feature-container {
                grid-template-columns: 1fr;
            }

            .search-input {
                flex-direction: column;
            }

            .search-input button {
                width: 100%;
            }

            .hero {
                padding: 55px 18px;
            }
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

                <form action="/FindLib/katalog.php" method="GET"
                      class="search-box" role="search">

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