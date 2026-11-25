<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode permintaan tidak diizinkan.');
}

// Validasi token CSRF
$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    http_response_code(403);
    exit('Permintaan tidak valid. Silakan muat ulang halaman.');
}

// Validasi ID buku
$id = filter_var(
    $_POST['id_buku'] ?? '',
    FILTER_VALIDATE_INT
);

if ($id === false || $id < 1) {
    http_response_code(400);
    exit('ID buku tidak valid.');
}

try {
    // Pastikan buku tersedia
    $stmt = $pdo->prepare(
        "SELECT id_buku FROM buku WHERE id_buku = :id"
    );
    $stmt->execute(['id' => $id]);

    if (!$stmt->fetch()) {
        http_response_code(404);
        exit('Data buku tidak ditemukan.');
    }

    // Hapus buku
    $stmt = $pdo->prepare(
        "DELETE FROM buku WHERE id_buku = :id"
    );

    $stmt->execute(['id' => $id]);

    header('Location: /FindLib/admin/buku/index.php?status=dihapus');
    exit;

} catch (PDOException $e) {
    error_log('Hapus buku gagal: ' . $e->getMessage());

    http_response_code(409);
    exit('Buku tidak dapat dihapus. Data mungkin masih digunakan pada riwayat lainnya.');
}
