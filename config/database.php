<?php
declare(strict_types=1);

// Konfigurasi database lokal FINDLIB
$config = require __DIR__ . '/../database/config.local.php';

try {
    // Membuat koneksi MySQL menggunakan PDO
    $pdo = new PDO(
        "mysql:host={$config['host']};" .
        "dbname={$config['dbname']};" .
        "charset={$config['charset']}",
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    return $pdo;

} catch (PDOException $e) {
    // Jangan tampilkan detail kredensial atau koneksi
    error_log('FINDLIB database connection failed: ' . $e->getMessage());

    throw new RuntimeException(
        'Koneksi database FINDLIB gagal.'
    );
}