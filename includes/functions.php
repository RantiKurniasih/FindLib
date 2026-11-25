<?php
declare(strict_types=1);

/**
 * Mengambil daftar buku berdasarkan kata kunci dan kategori.
 */
function getBooks(
    PDO $pdo,
    string $keyword = '',
    ?int $categoryId = null
): array {
    $sql = "
        SELECT
            b.id_buku,
            b.no_katalog,
            b.judul,
            b.penulis,
            b.penerbit,
            b.tahun_terbit,
            b.id_kategori,
            k.nama_kategori,
            b.id_rak,
            r.kode_rak,
            r.nama_rak,
            b.jumlah_stok,
            CASE
                WHEN b.jumlah_stok > 0
                    THEN 'Tersedia'
                ELSE 'Tidak Tersedia'
            END AS status_ketersediaan
        FROM buku b
        JOIN kategori k
            ON b.id_kategori = k.id_kategori
        JOIN lokasi_rak r
            ON b.id_rak = r.id_rak
        WHERE 1 = 1
    ";

    $params = [];
    $keyword = trim($keyword);

    if ($keyword !== '') {
        $sql .= "
            AND (
                b.judul LIKE :judul
                OR b.penulis LIKE :penulis
                OR b.no_katalog LIKE :katalog
            )
        ";

        $pattern = '%' . $keyword . '%';

        $params['judul'] = $pattern;
        $params['penulis'] = $pattern;
        $params['katalog'] = $pattern;
    }

    if ($categoryId !== null && $categoryId > 0) {
        $sql .= " AND b.id_kategori = :kategori";
        $params['kategori'] = $categoryId;
    }

    $sql .= " ORDER BY b.judul ASC";

    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $type = $key === 'kategori'
            ? PDO::PARAM_INT
            : PDO::PARAM_STR;

        $stmt->bindValue(':' . $key, $value, $type);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Mengambil detail buku berdasarkan ID.
 */
function getBookById(PDO $pdo, int $id): ?array
{
    if ($id < 1) {
        return null;
    }

    $stmt = $pdo->prepare("
        SELECT
            b.*,
            k.nama_kategori,
            r.kode_rak,
            r.nama_rak
        FROM buku b
        JOIN kategori k
            ON b.id_kategori = k.id_kategori
        JOIN lokasi_rak r
            ON b.id_rak = r.id_rak
        WHERE b.id_buku = :id
        LIMIT 1
    ");

    $stmt->execute([
        'id' => $id
    ]);

    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    return $book ?: null;
}

/**
 * Mengambil seluruh kategori buku.
 */
function getCategories(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT
            id_kategori,
            nama_kategori
        FROM kategori
        ORDER BY nama_kategori ASC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Menentukan status ketersediaan buku.
 */
function getAvailabilityStatus(int $stock): string
{
    return $stock > 0
        ? 'Tersedia'
        : 'Tidak Tersedia';
}