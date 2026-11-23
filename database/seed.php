<?php
/**
 * FINDLIB - Data Dummy untuk Pengujian
 *
 * Data ini hanya untuk pengujian sistem.
 * Bukan koleksi asli Ruang Baca FMIPA UHO.
 *
 * Jalankan melalui terminal:
 * php database/seed.php
 */

declare(strict_types=1);

// Membaca konfigurasi MySQL lokal
$config = require __DIR__ . '/config.local.php';

$host = $config['host'];
$database = $config['dbname'];
$username = $config['username'];
$password = $config['password'];
$charset = $config['charset'];

try {
    // Menghubungkan PHP dengan MySQL
    $pdo = new PDO(
        "mysql:host=$host;dbname=$database;charset=$charset",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Memulai transaksi database
    $pdo->beginTransaction();

    // =========================================
    // 1. DATA KATEGORI
    // =========================================

    $kategori = [
        'Matematika',
        'Fisika',
        'Kimia'
    ];

    $stmtKategori = $pdo->prepare(
        "INSERT INTO kategori (nama_kategori)
         VALUES (?)
         ON DUPLICATE KEY UPDATE
         nama_kategori = VALUES(nama_kategori)"
    );

    foreach ($kategori as $nama) {
        $stmtKategori->execute([$nama]);
    }

    // =========================================
    // 2. DATA LOKASI RAK
    // =========================================

    $rak = [
        ['R-01', 'Rak Matematika'],
        ['R-02', 'Rak Fisika'],
        ['R-03', 'Rak Kimia']
    ];

    $stmtRak = $pdo->prepare(
        "INSERT INTO lokasi_rak (kode_rak, nama_rak)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE
         nama_rak = VALUES(nama_rak)"
    );

    foreach ($rak as $item) {
        $stmtRak->execute($item);
    }

    // =========================================
    // 3. MENGAMBIL ID KATEGORI DAN RAK
    // =========================================

    $idKategori = $pdo->query(
        "SELECT nama_kategori, id_kategori FROM kategori"
    )->fetchAll(PDO::FETCH_KEY_PAIR);

    $idRak = $pdo->query(
        "SELECT kode_rak, id_rak FROM lokasi_rak"
    )->fetchAll(PDO::FETCH_KEY_PAIR);

    // =========================================
    // 4. DATA 10 BUKU DUMMY
    // =========================================

    $buku = [
        ['FL-001', 'Dasar-Dasar Aljabar', 'Andi Pratama', 'Pustaka Ilmu', 2021, 'Matematika', 'R-01', 5],
        ['FL-002', 'Pengantar Kalkulus', 'Rina Lestari', 'Cendekia Press', 2020, 'Matematika', 'R-01', 3],
        ['FL-003', 'Matematika Diskrit', 'Budi Santoso', 'Pustaka Ilmu', 2022, 'Matematika', 'R-01', 0],
        ['FL-004', 'Statistika Dasar', 'Dewi Anggraini', 'Edu Media', 2021, 'Matematika', 'R-01', 4],
        ['FL-005', 'Konsep Fisika Dasar', 'Arif Nugroho', 'Cendekia Press', 2019, 'Fisika', 'R-02', 6],
        ['FL-006', 'Mekanika Klasik', 'Sinta Maharani', 'Edu Media', 2020, 'Fisika', 'R-02', 2],
        ['FL-007', 'Pengantar Termodinamika', 'Rudi Hartono', 'Pustaka Ilmu', 2023, 'Fisika', 'R-02', 0],
        ['FL-008', 'Kimia Dasar', 'Nina Oktaviani', 'Cendekia Press', 2021, 'Kimia', 'R-03', 7],
        ['FL-009', 'Kimia Organik', 'Fajar Ramadhan', 'Edu Media', 2022, 'Kimia', 'R-03', 3],
        ['FL-010', 'Pengantar Biokimia', 'Maya Putri', 'Pustaka Ilmu', 2023, 'Kimia', 'R-03', 1]
    ];

    $stmtBuku = $pdo->prepare(
        "INSERT INTO buku
         (no_katalog, judul, penulis, penerbit,
          tahun_terbit, id_kategori, id_rak,
          jumlah_stok, stock_updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
         ON DUPLICATE KEY UPDATE
         id_buku = id_buku"
    );

    foreach ($buku as $item) {
        $stmtBuku->execute([
            $item[0],
            $item[1],
            $item[2],
            $item[3],
            $item[4],
            $idKategori[$item[5]],
            $idRak[$item[6]],
            $item[7]
        ]);
    }

    // Menyimpan seluruh perubahan
    $pdo->commit();

    echo "Berhasil menyiapkan data dummy FINDLIB!" . PHP_EOL;
    echo "3 kategori, 3 lokasi rak, dan 10 buku contoh." . PHP_EOL;

} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "Gagal menjalankan seed: "
        . $e->getMessage() . PHP_EOL;

    exit(1);
}