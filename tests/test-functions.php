<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== PENGUJIAN BACKEND FINDLIB ===\n\n";

$books = getBooks($pdo);
echo "1. getBooks(): BERHASIL\n";
echo "Jumlah buku: " . count($books) . "\n\n";

$categories = getCategories($pdo);
echo "2. getCategories(): BERHASIL\n";
echo "Jumlah kategori: " . count($categories) . "\n\n";

echo "3. getAvailabilityStatus():\n";
echo "Stok 5: " . getAvailabilityStatus(5) . "\n";
echo "Stok 0: " . getAvailabilityStatus(0) . "\n\n";

echo "4. getBookById():\n";

if (!empty($books)) {
    $id = (int) $books[0]['id_buku'];
    $book = getBookById($pdo, $id);

    echo "Judul: " . ($book['judul'] ?? 'Tidak ditemukan');
} else {
    echo "Belum ada data buku untuk diuji.";
}
echo "\n5. Pengujian pencarian buku:\n";

$hasil = getBooks($pdo, 'Matematika');

echo "Hasil pencarian Matematika: "
    . count($hasil) . " buku\n";

echo "\n6. Pengujian filter kategori:\n";

if (!empty($categories)) {
    $idKategori = (int) $categories[0]['id_kategori'];

    $hasilKategori = getBooks($pdo, '', $idKategori);

    echo "Jumlah buku dalam kategori: "
        . count($hasilKategori) . "\n";
}
echo "\n\n=== SELESAI ===";

