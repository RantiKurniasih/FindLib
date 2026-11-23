# Dokumentasi Database FINDLIB

## 1. Informasi Database

FINDLIB (Find Library) adalah Sistem Informasi
Ketersediaan Buku Ruang Baca FMIPA
Universitas Halu Oleo.

Database digunakan untuk menyimpan katalog buku,
kategori, lokasi rak, akun admin, dan riwayat stok.

- Nama database: `findlib`
- DBMS: MySQL 8.0+
- Engine: InnoDB
- Character set: utf8mb4
- Collation: utf8mb4_unicode_ci
- Backend: PHP Native dengan PDO

## 2. Struktur Tabel

Database FINDLIB memiliki lima tabel utama.

### 2.1 Tabel pengguna

Menyimpan akun administrator.

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id_pengguna | INT UNSIGNED | Primary Key |
| nama | VARCHAR(100) | Nama admin |
| username | VARCHAR(50) | UNIQUE |
| email | VARCHAR(150) | UNIQUE |
| password_hash | VARCHAR(255) | Hash password |
| status_aktif | TINYINT(1) | 1 aktif, 0 nonaktif |

Password dibuat menggunakan `password_hash()`
dan diperiksa menggunakan `password_verify()`.

### 2.2 Tabel kategori

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id_kategori | INT UNSIGNED | Primary Key |
| nama_kategori | VARCHAR(100) | UNIQUE |

### 2.3 Tabel lokasi_rak

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id_rak | INT UNSIGNED | Primary Key |
| kode_rak | VARCHAR(20) | UNIQUE |
| nama_rak | VARCHAR(100) | Nama lokasi rak |

### 2.4 Tabel buku

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id_buku | INT UNSIGNED | Primary Key |
| no_katalog | VARCHAR(50) | UNIQUE |
| judul | VARCHAR(255) | Judul buku |
| penulis | VARCHAR(150) | Penulis |
| penerbit | VARCHAR(150) | Penerbit |
| tahun_terbit | SMALLINT UNSIGNED | Tahun |
| id_kategori | INT UNSIGNED | Foreign Key |
| id_rak | INT UNSIGNED | Foreign Key |
| jumlah_stok | INT UNSIGNED | Stok >= 0 |
| updated_at | TIMESTAMP | Pembaruan data |
| stock_updated_at | TIMESTAMP NULL | Pembaruan stok |

Status ketersediaan dihitung otomatis:

- Stok > 0: Tersedia
- Stok = 0: Tidak Tersedia

Tidak ada kolom status yang diisi manual.

### 2.5 Tabel riwayat_status

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id_riwayat | BIGINT UNSIGNED | Primary Key |
| id_buku | INT UNSIGNED | Foreign Key |
| id_pengguna | INT UNSIGNED | Foreign Key |
| stok_sebelum | INT UNSIGNED | Stok awal |
| stok_sesudah | INT UNSIGNED | Stok akhir |
| waktu_perubahan | TIMESTAMP | Waktu perubahan |

## 3. Relasi Database

Relasi antar tabel:

1. kategori (1) ke buku (N)
2. lokasi_rak (1) ke buku (N)
3. buku (1) ke riwayat_status (N)
4. pengguna (1) ke riwayat_status (N)

Foreign Key menggunakan ON DELETE RESTRICT
untuk mencegah penghapusan data induk
yang masih digunakan.

## 4. View Katalog

View `view_katalog` menggabungkan data
buku, kategori, dan lokasi rak.

Contoh query:

```sql
SELECT * FROM view_katalog
ORDER BY judul ASC;
```

## 5. Query Katalog dengan JOIN

```sql
SELECT
    b.no_katalog,
    b.judul,
    b.penulis,
    k.nama_kategori,
    r.kode_rak,
    r.nama_rak,
    b.jumlah_stok,
    b.stock_updated_at
FROM buku b
JOIN kategori k
    ON b.id_kategori = k.id_kategori
JOIN lokasi_rak r
    ON b.id_rak = r.id_rak;
```

## 6. Query Pencarian Buku

Pencarian berdasarkan judul, penulis,
atau kategori:

```sql
SELECT *
FROM view_katalog
WHERE judul LIKE :keyword
   OR penulis LIKE :keyword
   OR nama_kategori LIKE :keyword;
```

Parameter `:keyword` diisi melalui PDO
dengan nilai seperti `%matematika%`.

Jika menggunakan PDO MySQL dengan
emulated prepares dinonaktifkan,
gunakan nama parameter yang berbeda
untuk setiap kondisi atau placeholder `?`.

## 7. Query Riwayat Stok

```sql
SELECT
    rs.id_riwayat,
    b.judul,
    p.nama AS nama_admin,
    rs.stok_sebelum,
    rs.stok_sesudah,
    rs.waktu_perubahan
FROM riwayat_status rs
JOIN buku b
    ON rs.id_buku = b.id_buku
JOIN pengguna p
    ON rs.id_pengguna = p.id_pengguna
ORDER BY rs.waktu_perubahan DESC,
         rs.id_riwayat DESC;
```

## 8. Aturan Pembaruan Stok

Perubahan jumlah stok wajib disertai
pencatatan riwayat dalam satu transaksi.

Alur backend:

1. Mulai transaksi PDO.
2. Kunci dan baca stok buku dengan SELECT FOR UPDATE.
3. Validasi stok baru tidak negatif.
4. UPDATE jumlah_stok pada tabel buku.
5. INSERT perubahan ke tabel riwayat_status.
6. COMMIT jika seluruh operasi berhasil.
7. ROLLBACK jika terjadi kesalahan.

Trigger `trg_buku_before_update` mengatur
`stock_updated_at` saat jumlah stok berubah.

Trigger ini tidak otomatis menulis riwayat.
Backend wajib menjalankan kedua operasi
secara atomik.

## 9. Data Dummy

Data pengujian dibuat menggunakan:

```bash
php database/seed.php
```

Data contoh mencakup:

- 3 kategori: Matematika, Fisika, Kimia
- 3 lokasi rak: R-01, R-02, R-03
- 10 buku dengan nomor FL-001 sampai FL-010

Seluruh data buku merupakan data dummy,
bukan koleksi asli Ruang Baca FMIPA UHO.

## 10. Keamanan

- Gunakan PDO prepared statements.
- Jangan menyimpan password admin dalam SQL.
- Gunakan password_hash() dan password_verify().
- Jangan unggah config.local.php ke GitHub.
- Jangan unggah test-password.php ke GitHub.
- Batasi akses ke skrip setup dan seed.
- Jangan mengubah nama tabel atau kolom
  tanpa persetujuan tim FINDLIB.