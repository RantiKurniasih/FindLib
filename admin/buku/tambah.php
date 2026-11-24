<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

wajibLogin();

// Membuat token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

// Mengambil data kategori dan lokasi rak
$kategori = $pdo->query(
    "SELECT id_kategori, nama_kategori
     FROM kategori ORDER BY nama_kategori"
)->fetchAll();

$rak = $pdo->query(
    "SELECT id_rak, kode_rak, nama_rak
     FROM lokasi_rak ORDER BY kode_rak"
)->fetchAll();

// Proses tambah buku
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        $error = 'Permintaan tidak valid. Muat ulang halaman.';
    } else {
        $no_katalog = trim($_POST['no_katalog'] ?? '');
        $judul = trim($_POST['judul'] ?? '');
        $penulis = trim($_POST['penulis'] ?? '');
        $penerbit = trim($_POST['penerbit'] ?? '');
        $tahun = filter_var($_POST['tahun_terbit'] ?? '', FILTER_VALIDATE_INT);
        $id_kategori = filter_var($_POST['id_kategori'] ?? '', FILTER_VALIDATE_INT);
        $id_rak = filter_var($_POST['id_rak'] ?? '', FILTER_VALIDATE_INT);
        $stok = filter_var($_POST['jumlah_stok'] ?? '', FILTER_VALIDATE_INT);

        if (
            $no_katalog === '' ||
            $judul === '' ||
            $penulis === '' ||
            $penerbit === '' ||
            $tahun === false ||
            $tahun < 1000 ||
            $tahun > (int) date('Y') ||
            $id_kategori === false ||
            $id_rak === false ||
            $stok === false ||
            $stok < 0
        ) {
            $error = 'Periksa kembali semua data yang dimasukkan.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO buku
                    (no_katalog, judul, penulis, penerbit,
                     tahun_terbit, id_kategori, id_rak, jumlah_stok)
                    VALUES
                    (:no_katalog, :judul, :penulis, :penerbit,
                     :tahun, :kategori, :rak, :stok)"
                );

                $stmt->execute([
                    'no_katalog' => $no_katalog,
                    'judul' => $judul,
                    'penulis' => $penulis,
                    'penerbit' => $penerbit,
                    'tahun' => $tahun,
                    'kategori' => $id_kategori,
                    'rak' => $id_rak,
                    'stok' => $stok
                ]);

                header('Location: /FindLib/admin/buku/index.php?status=berhasil');
                exit;

            } catch (PDOException $e) {
                error_log('Tambah buku gagal: ' . $e->getMessage());
                $error = 'Gagal menambahkan buku. Periksa nomor katalog atau data lainnya.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - FINDLIB</title>
</head>
<body>

<h2>Tambah Buku</h2>

<?php if ($error !== ''): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<form method="POST">

    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

    <p>
        <label>Nomor Katalog</label><br>
        <input type="text" name="no_katalog" required>
    </p>

    <p>
        <label>Judul Buku</label><br>
        <input type="text" name="judul" required>
    </p>

    <p>
        <label>Penulis</label><br>
        <input type="text" name="penulis" required>
    </p>

    <p>
        <label>Penerbit</label><br>
        <input type="text" name="penerbit" required>
    </p>

    <p>
        <label>Tahun Terbit</label><br>
        <input type="number" name="tahun_terbit"
               min="1000" max="<?= date('Y') ?>" required>
    </p>

    <p>
        <label>Kategori</label><br>
        <select name="id_kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategori as $item): ?>
                <option value="<?= (int) $item['id_kategori'] ?>">
                    <?= htmlspecialchars($item['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label>Lokasi Rak</label><br>
        <select name="id_rak" required>
            <option value="">-- Pilih Rak --</option>
            <?php foreach ($rak as $item): ?>
                <option value="<?= (int) $item['id_rak'] ?>">
                    <?= htmlspecialchars($item['kode_rak'] . ' - ' . $item['nama_rak'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label>Jumlah Stok</label><br>
        <input type="number" name="jumlah_stok" min="0" required>
    </p>

    <button type="submit">Simpan Buku</button>
    <a href="index.php">Kembali</a>

</form>

</body>
</html>
