<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

$admin = requireAdmin($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metode tidak diizinkan.');
}

if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Token keamanan tidak valid.');
}

$id = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($id === false || $id === null) {
    http_response_code(400);
    exit('ID akun tidak valid.');
}

if ($id === (int) $admin['id_pengguna']) {
    http_response_code(403);
    exit('Status akun sendiri tidak boleh diubah.');
}

try {
    $pdo->beginTransaction();

    // Kunci baris akun selama perubahan berlangsung.
    $stmt = $pdo->query(
        'SELECT id_pengguna, status_aktif
         FROM pengguna
         ORDER BY id_pengguna
         FOR UPDATE'
    );

    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $target = null;
    $jumlahAktif = 0;

    foreach ($accounts as $account) {
        if ((int) $account['status_aktif'] === 1) {
            $jumlahAktif++;
        }

        if ((int) $account['id_pengguna'] === $id) {
            $target = $account;
        }
    }

    if ($target === null) {
        $pdo->rollBack();
        http_response_code(404);
        exit('Akun tidak ditemukan.');
    }

    $statusLama = (int) $target['status_aktif'];
    $statusBaru = $statusLama === 1 ? 0 : 1;

    if ($statusBaru === 0 && $jumlahAktif <= 1) {
        $pdo->rollBack();
        http_response_code(409);
        exit('Minimal satu akun pengelola harus tetap aktif.');
    }

    $update = $pdo->prepare(
        'UPDATE pengguna
         SET status_aktif = :status
         WHERE id_pengguna = :id'
    );

    $update->execute([
        'status' => $statusBaru,
        'id' => $id
    ]);

    $pdo->commit();

    header('Location: index.php', true, 303);
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log($e->getMessage());
    http_response_code(500);
    exit('Gagal mengubah status akun.');
}