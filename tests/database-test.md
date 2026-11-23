# Dokumentasi Pengujian Database FINDLIB

## 1. Informasi Pengujian

- Proyek: FINDLIB (Find Library)
- Sistem: Informasi Ketersediaan Buku
- Lokasi: Ruang Baca FMIPA Universitas Halu Oleo
- Penguji: Developer 3 (Database, Integrasi & Testing)
- DBMS: MySQL 8.4.3
- PHP: 8.3.33
- Tools: Laragon, phpMyAdmin, VS Code

## 2. Tujuan Pengujian

Memastikan database FINDLIB dapat:

1. Menyimpan informasi buku.
2. Menghubungkan buku dengan kategori dan rak.
3. Menampilkan status ketersediaan berdasarkan stok.
4. Mencegah nomor katalog duplikat.
5. Mencegah stok negatif.
6. Mencatat perubahan stok.
7. Menyimpan password admin dalam bentuk hash.
8. Mendukung transaksi database.

## 3. Skenario Pengujian

Keterangan:
- Lulus: sudah diuji dan hasilnya sesuai.
- Belum diuji: pengujian belum dilakukan.

| ID | Skenario | Hasil yang Diharapkan | Status |
|---|---|---|---|
| DB-01 | Import findlib.sql | Database berhasil dibuat | Lulus |
| DB-02 | Periksa 5 tabel | Semua tabel tersedia | Lulus |
| DB-03 | Periksa Foreign Key | Terdapat 4 relasi | Lulus |
| DB-04 | Periksa trigger | Trigger stok tersedia | Lulus |
| DB-05 | Jalankan seed.php | Data dummy berhasil dimasukkan | Lulus |
| DB-06 | Periksa katalog | Terdapat 10 buku | Lulus |
| DB-07 | Status stok > 0 | Tersedia | Lulus |
| DB-08 | Status stok = 0 | Tidak Tersedia | Lulus |
| DB-09 | Buat admin | Akun admin berhasil dibuat | Lulus |
| DB-10 | Verifikasi password | password_verify() berhasil | Lulus |
| DB-11 | Nomor katalog duplikat | INSERT ditolak | Belum diuji |
| DB-12 | Stok negatif | UPDATE ditolak | Belum diuji |
| DB-13 | Foreign Key tidak valid | INSERT ditolak | Belum diuji |
| DB-14 | Pembaruan stok | stock_updated_at berubah | Belum diuji |
| DB-15 | Perubahan judul | stock_updated_at tetap | Belum diuji |
| DB-16 | Riwayat perubahan stok | Riwayat tersimpan | Belum diuji |
| DB-17 | Transaksi gagal | ROLLBACK berhasil | Belum diuji |
| DB-18 | Jalankan seed ulang | Tidak menambah buku duplikat | Belum diuji |

## 4. Query Pengujian

### DB-01 dan DB-02: Struktur database

```sql
SHOW FULL TABLES FROM findlib;
```

Hasil yang diharapkan:
5 tabel utama dan 1 view.

### DB-03: Foreign Key

```sql
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'findlib'
  AND REFERENCED_TABLE_NAME IS NOT NULL;
```

Hasil yang diharapkan: 4 relasi.

### DB-04: Trigger

```sql
SHOW TRIGGERS FROM findlib;
```

Hasil yang diharapkan:
trg_buku_before_update tersedia.

### DB-06 sampai DB-08: Katalog dan status

```sql
SELECT
    no_katalog,
    judul,
    jumlah_stok,
    status_ketersediaan
FROM view_katalog
ORDER BY no_katalog;
```

Hasil yang diharapkan:
10 buku, terdiri dari 8 buku tersedia
dan 2 buku tidak tersedia.

### DB-11: Nomor katalog duplikat

```sql
INSERT INTO buku (
    no_katalog, judul, penulis, penerbit,
    tahun_terbit, id_kategori, id_rak, jumlah_stok
)
SELECT
    'FL-001', 'Buku Uji Duplikat',
    'Penulis Uji', 'Penerbit Uji',
    2024, id_kategori, id_rak, 1
FROM buku
WHERE no_katalog = 'FL-001';
```

Hasil yang diharapkan:
Error Duplicate entry.

### DB-12: Stok negatif

```sql
UPDATE buku
SET jumlah_stok = -1
WHERE no_katalog = 'FL-001';
```

Hasil yang diharapkan:
MySQL menolak nilai stok negatif.

Jangan menjalankan query pengujian negatif
di database produksi.

### DB-13: Foreign Key tidak valid

```sql
INSERT INTO buku (
    no_katalog, judul, penulis, penerbit,
    tahun_terbit, id_kategori, id_rak, jumlah_stok
)
VALUES (
    'FL-TEST-FK', 'Buku Pengujian FK',
    'Penulis Uji', 'Penerbit Uji',
    2024, 999999, 999999, 1
);
```

Hasil yang diharapkan:
MySQL menolak Foreign Key tidak valid.

### DB-14 dan DB-15: Trigger waktu stok

Lakukan pengujian pada database lokal
menggunakan data dummy dan catat
nilai stock_updated_at sebelum dan sesudah.

Perubahan jumlah stok harus memperbarui
stock_updated_at.

Perubahan judul tidak boleh memperbarui
stock_updated_at.

### DB-16 dan DB-17: Riwayat dan transaksi

Pengujian dilakukan setelah integrasi
dengan backend PHP menggunakan PDO.

Proses pembaruan stok harus:

1. Memulai transaksi.
2. Mengunci dan membaca stok.
3. Memperbarui jumlah_stok.
4. Menambahkan riwayat_status.
5. COMMIT jika berhasil.
6. ROLLBACK jika gagal.

Hasil yang diharapkan:
Perubahan stok dan pencatatan riwayat
berhasil atau gagal secara bersamaan.

## 5. Data Pengujian

Data dummy terdiri dari:

- 3 kategori
- 3 lokasi rak
- 10 buku fiktif

Data tersebut bukan koleksi asli
Ruang Baca FMIPA Universitas Halu Oleo.

## 6. Kesimpulan Sementara

Struktur database, relasi Foreign Key,
view katalog, trigger, koneksi PDO,
data dummy, dan verifikasi password admin
telah melalui pengujian awal.

Pengujian constraint negatif,
perubahan stok, pencatatan riwayat,
dan transaksi database masih perlu dilakukan.

Status pengujian akan diperbarui
setelah seluruh skenario selesai dijalankan.