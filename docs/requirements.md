# Software Requirements FINDLIB

## 1. Identitas Dokumen

- Nama sistem: FINDLIB (Find Library)
- Institusi: FMIPA Universitas Halu Oleo
- Jenis aplikasi: Website informasi ketersediaan buku
- Status: Draft — Perlu dikonfirmasi

## 2. Deskripsi Sistem

FINDLIB merupakan sistem informasi berbasis website untuk menampilkan informasi koleksi dan ketersediaan buku Ruang Baca FMIPA Universitas Halu Oleo.

Sistem terdiri atas dua aktor, yaitu Pengunjung dan Admin.

Pengunjung menggunakan sistem untuk memperoleh informasi buku, sedangkan Admin menggunakan sistem untuk mengelola data.

## 3. Kebutuhan Fungsional

Kebutuhan fungsional adalah fungsi yang harus disediakan oleh sistem.

### FR-01 — Menampilkan Katalog Buku

**Aktor:** Pengunjung

Sistem harus menyediakan halaman katalog yang dapat diakses tanpa login dan menampilkan daftar buku yang tersimpan.

**Kriteria verifikasi:**

- Katalog dapat dibuka tanpa login.
- Buku yang tersimpan ditampilkan pada katalog.
- Pengunjung tidak memperoleh hak pengelolaan.

### FR-02 — Mencari Buku

**Aktor:** Pengunjung

Sistem harus memungkinkan pengunjung mencari buku menggunakan kata kunci.

**Kriteria verifikasi:**

- Pengunjung dapat memasukkan kata kunci.
- Sistem menampilkan hasil yang sesuai.
- Sistem menampilkan pesan apabila hasil tidak ditemukan.

### FR-03 — Melihat Detail Buku

**Aktor:** Pengunjung

Sistem harus menampilkan informasi detail buku yang dipilih dari katalog.

Informasi minimal meliputi:

- Judul buku.
- Penulis.
- Kategori.
- Lokasi rak.

Informasi tambahan seperti penerbit, tahun terbit, ISBN, dan gambar sampul: **Perlu dikonfirmasi**.

### FR-04 — Melihat Ketersediaan Buku

**Aktor:** Pengunjung

Sistem harus menampilkan jumlah stok, status ketersediaan, dan waktu pembaruan data.

**Aturan bisnis:**

- Stok lebih dari 0: Tersedia.
- Stok sama dengan 0: Tidak Tersedia.
- Stok tidak boleh negatif.
- Waktu pembaruan harus berasal dari data perubahan yang tercatat.

### FR-05 — Login dan Logout Admin

**Aktor:** Admin

Sistem harus menyediakan autentikasi untuk membatasi akses halaman pengelolaan.

**Kriteria verifikasi:**

- Login berhasil untuk kredensial valid.
- Login ditolak untuk kredensial tidak valid.
- Halaman admin terlindungi dari akses tanpa login.
- Logout mengakhiri sesi pengguna.

### FR-06 — Dashboard Admin

**Aktor:** Admin

Sistem harus menyediakan dashboard yang menampilkan ringkasan data koleksi.

Informasi yang diusulkan:

- Total judul buku.
- Total stok buku.
- Jumlah judul yang tersedia.
- Jumlah judul dengan stok nol.

Rincian indikator dashboard: **Perlu dikonfirmasi**.

### FR-07 — Mengelola Data Buku

**Aktor:** Admin

Sistem harus memungkinkan admin:

- Menambahkan buku.
- Melihat daftar buku.
- Mengubah informasi buku.
- Menghapus data buku.

Sistem harus melakukan validasi terhadap data wajib sebelum menyimpan perubahan.

### FR-08 — Mengelola Stok Buku

**Aktor:** Admin

Sistem harus memungkinkan admin memperbarui jumlah stok buku.

**Kriteria verifikasi:**

- Admin dapat mengubah stok.
- Sistem menolak stok negatif.
- Perubahan stok disimpan.
- Status ketersediaan mengikuti jumlah stok terbaru.
- Perubahan stok yang berhasil dicatat dalam riwayat.

### FR-09 — Mengelola Kategori Buku

**Aktor:** Admin

Sistem harus memungkinkan admin menambahkan, melihat, mengubah, dan menghapus kategori buku.

Sistem harus menjaga konsistensi relasi antara kategori dan buku.

### FR-10 — Mengelola Lokasi Rak

**Aktor:** Admin

Sistem harus memungkinkan admin menambahkan, melihat, mengubah, dan menghapus data rak.

Setiap buku dapat dihubungkan dengan lokasi rak sesuai struktur data yang disepakati.

### FR-11 — Mengelola Akun Admin

**Aktor:** Admin yang berwenang

Sistem harus menyediakan fungsi pengelolaan akun admin.

Fungsi yang diusulkan:

- Melihat daftar akun.
- Menambahkan akun.
- Mengubah informasi akun.
- Mengelola kredensial secara aman.

Aturan kewenangan dan penghapusan akun: **Perlu dikonfirmasi**.

### FR-12 — Melihat Riwayat Perubahan Stok

**Aktor:** Admin

Sistem harus mencatat dan menampilkan riwayat perubahan stok.

Informasi minimal:

- Identitas buku.
- Jumlah stok sebelum perubahan.
- Jumlah stok setelah perubahan.
- Waktu perubahan.
- Admin yang melakukan perubahan.

Riwayat hanya dapat diakses oleh admin yang telah login.

## 4. Matriks Keterlacakan Kebutuhan

| Requirement | Product Backlog | Aktor |
|---|---|---|
| FR-01 | PB-01 | Pengunjung |
| FR-02 | PB-01 | Pengunjung |
| FR-03 | PB-02 | Pengunjung |
| FR-04 | PB-02 | Pengunjung |
| FR-05 | PB-03 | Admin |
| FR-06 | PB-03 | Admin |
| FR-07 | PB-04 | Admin |
| FR-08 | PB-04 | Admin |
| FR-09 | PB-05 | Admin |
| FR-10 | PB-05 | Admin |
| FR-11 | PB-06 | Admin |
| FR-12 | PB-06 | Admin |

Pemetaan tersebut merupakan usulan awal dan harus disesuaikan dengan dokumen kebutuhan resmi kelompok.

## 5. Kebutuhan Nonfungsional

### NFR-01 — Kemudahan Penggunaan

Antarmuka harus mudah dipahami oleh pengunjung dan admin.

### NFR-02 — Keamanan Akses

Halaman pengelolaan hanya dapat diakses oleh admin yang terautentikasi. Kata sandi harus disimpan menggunakan hashing yang aman.

### NFR-03 — Integritas Data

Sistem harus memvalidasi input dan menjaga konsistensi data antar tabel database.

### NFR-04 — Kompatibilitas

Aplikasi ditujukan untuk digunakan melalui browser modern pada komputer. Dukungan perangkat seluler dan daftar browser pengujian: **Perlu dikonfirmasi**.

### NFR-05 — Pemeliharaan

Struktur kode dan database harus dapat dipahami serta dipelihara oleh tim pengembang.

### NFR-06 — Keandalan Informasi

Informasi stok dan status harus konsisten dengan data terakhir yang berhasil disimpan.

Target waktu respons, jumlah pengguna bersamaan, dan ukuran data maksimum: **Perlu dikonfirmasi**.

## 6. Aturan Bisnis

1. Pengunjung dapat melihat katalog tanpa login.
2. Hanya Admin yang dapat mengelola data.
3. Jumlah stok tidak boleh negatif.
4. Status ketersediaan ditentukan berdasarkan stok.
5. Informasi ketersediaan mengikuti pembaruan yang dilakukan admin.
6. Perubahan stok yang berhasil harus tercatat.
7. Penghapusan kategori atau rak tidak boleh menyebabkan relasi data menjadi tidak konsisten.
8. FINDLIB tidak menyediakan peminjaman atau reservasi buku.

## 7. Batasan Pengembangan

- Menggunakan PHP Native.
- Menggunakan MySQL.
- Antarmuka menggunakan HTML, CSS, dan JavaScript.
- Pengembangan lokal menggunakan Laragon.
- Kolaborasi menggunakan Git dan GitHub.
- Durasi pengembangan direncanakan empat minggu.

## 8. Asumsi dan Hal yang Perlu Dikonfirmasi

1. Data awal buku yang akan digunakan.
2. Atribut wajib setiap buku.
3. Mekanisme pembaruan stok.
4. Detail indikator dashboard.
5. Aturan pembuatan dan penghapusan akun admin.
6. Aturan penghapusan kategori dan rak.
7. Dukungan browser dan perangkat.
8. Kebutuhan hosting setelah proyek selesai.
9. Kesesuaian penomoran FR-01 sampai FR-12 dengan laporan.

## 9. Kriteria Penerimaan Keseluruhan Sistem

Sistem dapat diajukan untuk penerimaan apabila:

1. Katalog dapat diakses tanpa login.
2. Pencarian dan detail buku bekerja.
3. Stok, status, lokasi rak, dan waktu pembaruan ditampilkan dengan benar.
4. Admin dapat login dan logout.
5. Fitur pengelolaan buku, stok, kategori, dan rak bekerja.
6. Pengelolaan akun admin sesuai kewenangan.
7. Riwayat perubahan stok dapat ditampilkan.
8. Akses pengunjung dan admin terpisah.
9. Tidak terdapat fitur peminjaman atau reservasi.
10. Seluruh acceptance criteria yang disepakati telah diverifikasi.
