# Skenario Sprint Review FINDLIB

## 1. Informasi Dokumen

- **Proyek:** FINDLIB (Find Library)
- **Metode Pengembangan:** Scrum
- **Durasi Proyek:** 4 minggu (4 Sprint)
- **Status Dokumen:** Rencana Sprint Review

## 2. Tujuan Sprint Review

Sprint Review dilaksanakan untuk memeriksa hasil pengembangan pada setiap Sprint dan mengumpulkan masukan terhadap produk FINDLIB.

Product Owner memeriksa kesesuaian hasil pengembangan dengan Product Backlog, kebutuhan sistem, dan acceptance criteria yang telah disepakati.

Fitur yang belum memenuhi kriteria penerimaan dicatat sebagai bahan perbaikan dan tindak lanjut bersama tim pengembang.

## 3. Persiapan Demonstrasi

Sebelum Sprint Review, tim menyiapkan:

1. Aplikasi FINDLIB sesuai fitur yang telah dikembangkan.
2. Data buku contoh, termasuk buku dengan stok tersedia dan stok nol.
3. Data kategori dan lokasi rak.
4. Akun admin untuk demonstrasi.
5. Data riwayat perubahan stok apabila fitur telah tersedia.

Data demonstrasi digunakan untuk memeriksa fungsi sistem dan tidak dianggap sebagai data koleksi sebenarnya apabila belum diverifikasi.

## 4. Rencana Sprint Review

### Sprint 1 — Analisis Kebutuhan dan Perancangan

**Fokus:** Kickoff proyek, analisis kebutuhan, Product Backlog, UML/ERD, dan wireframe.

**Skenario review:**

1. Tim mempresentasikan kebutuhan fungsional dan nonfungsional FINDLIB.
2. Tim menunjukkan Product Backlog beserta prioritasnya.
3. Tim menjelaskan rancangan UML dan ERD.
4. Tim memperlihatkan wireframe halaman FINDLIB.
5. Product Owner memberikan masukan terhadap kesesuaian rancangan dengan kebutuhan pengguna.

**Hasil yang diharapkan:** Dokumen kebutuhan dan rancangan FINDLIB telah disusun serta dapat ditinjau bersama tim.

### Sprint 2 — Katalog, Pencarian, Detail Buku, dan Login Admin

**Fokus:** Implementasi fitur pengunjung dan autentikasi dasar admin.

**DEMO-01 — Mengakses Beranda dan Katalog**

Requirement: FR-01

Langkah:
1. Buka website FINDLIB.
2. Akses beranda dan katalog tanpa login.
3. Periksa daftar buku yang ditampilkan.

Hasil yang diharapkan: Pengunjung dapat melihat beranda dan katalog buku tanpa login.

**DEMO-02 — Mencari Buku**

Requirement: FR-02

Langkah:
1. Cari buku berdasarkan judul.
2. Cari buku berdasarkan penulis.
3. Cari buku berdasarkan kategori.
4. Masukkan kata kunci yang tidak memiliki hasil.

Hasil yang diharapkan: Sistem menampilkan hasil pencarian yang sesuai atau informasi ketika buku tidak ditemukan.

**DEMO-03 — Melihat Detail dan Lokasi Buku**

Requirement: FR-03

Langkah:
1. Pilih salah satu buku pada katalog.
2. Buka halaman detail buku.
3. Periksa nomor katalog, judul, pengarang, penerbit, tahun terbit, dan kategori.
4. Periksa informasi lokasi rak buku.

Hasil yang diharapkan: Detail dan lokasi rak ditampilkan sesuai data buku.

**DEMO-04 — Melihat Status Ketersediaan**

Requirement: FR-04

Langkah:
1. Buka buku dengan stok lebih dari nol.
2. Periksa jumlah stok dan status ketersediaan.
3. Buka buku dengan stok nol.
4. Periksa status ketersediaan dan informasi waktu pembaruan.

Hasil yang diharapkan: Buku dengan stok lebih dari nol berstatus "Tersedia", sedangkan buku dengan stok nol berstatus "Tidak Tersedia".

**DEMO-05 — Login Admin**

Requirement: FR-05

Langkah:
1. Buka halaman login admin.
2. Masukkan data login yang tidak valid.
3. Periksa penolakan akses.
4. Masukkan data login yang valid.
5. Periksa akses menuju halaman admin.

Hasil yang diharapkan: Hanya admin dengan data login valid yang dapat mengakses halaman pengelolaan.

### Sprint 3 — Dashboard dan Pengelolaan Data Buku

**Fokus:** Implementasi dashboard, pengelolaan buku, kategori, rak, dan stok.

**DEMO-06 — Melihat Dashboard Admin**

Requirement: FR-06

Langkah:
1. Login sebagai admin.
2. Buka halaman dashboard.
3. Periksa ringkasan informasi koleksi buku.

Hasil yang diharapkan: Dashboard menampilkan ringkasan data sesuai informasi yang tersimpan dalam sistem.

**DEMO-07 — Menambahkan Buku**

Requirement: FR-07

Langkah:
1. Buka halaman pengelolaan buku.
2. Tambahkan data buku baru.
3. Tentukan kategori, lokasi rak, dan jumlah stok awal.
4. Simpan data.
5. Periksa buku pada daftar koleksi.

Hasil yang diharapkan: Buku baru berhasil disimpan dan ditampilkan pada sistem.

**DEMO-08 — Mengedit Buku**

Requirement: FR-08

Langkah:
1. Pilih salah satu buku.
2. Ubah informasi buku.
3. Simpan perubahan.
4. Periksa kembali data buku.

Hasil yang diharapkan: Perubahan informasi buku berhasil tersimpan.

**DEMO-09 — Menghapus Buku**

Requirement: FR-09

Langkah:
1. Pilih data buku contoh.
2. Jalankan fitur hapus.
3. Konfirmasi penghapusan sesuai mekanisme aplikasi.
4. Periksa daftar buku.

Hasil yang diharapkan: Buku yang dihapus tidak lagi ditampilkan dalam daftar koleksi aktif.

**DEMO-10 — Mengelola Stok Buku**

Requirement: FR-10

Langkah:
1. Pilih buku yang akan diperbarui stoknya.
2. Ubah jumlah stok buku.
3. Simpan perubahan.
4. Periksa jumlah stok dan status ketersediaan pada katalog.

Hasil yang diharapkan: Perubahan stok tersimpan dan status ketersediaan diperbarui secara otomatis.

**DEMO-11 — Mengelola Kategori dan Lokasi Rak**

Requirement: FR-11

Langkah:
1. Tambahkan kategori buku.
2. Ubah informasi kategori.
3. Tambahkan lokasi rak.
4. Ubah kode atau nama rak.
5. Periksa kategori dan lokasi rak pada data buku.

Hasil yang diharapkan: Data kategori dan lokasi rak dapat ditambahkan, diperbarui, dan digunakan pada informasi buku.

### Sprint 4 — Riwayat Stok, Integrasi, dan Pengujian

**Fokus:** Penyelesaian riwayat stok, pengelolaan akun admin, integrasi, perbaikan, dan pengujian sistem.

**DEMO-12 — Memeriksa Riwayat Perubahan Stok**

Requirement: FR-10

Langkah:
1. Login sebagai admin.
2. Ubah stok salah satu buku.
3. Buka fitur riwayat perubahan stok.
4. Periksa apakah perubahan tercatat.

Hasil yang diharapkan: Sistem menyimpan riwayat perubahan stok sesuai ketentuan yang disepakati tim.

**DEMO-13 — Mengelola Akun Admin dan Logout**

Requirement: FR-12

Langkah:
1. Login menggunakan akun admin yang berwenang.
2. Buka fitur pengelolaan akun.
3. Periksa fungsi pengelolaan akun yang tersedia.
4. Lakukan logout.
5. Coba mengakses kembali halaman pengelolaan tanpa login.

Hasil yang diharapkan: Pengelolaan akun berjalan sesuai kewenangan dan halaman admin tidak dapat diakses tanpa autentikasi.

**DEMO-14 — Integrasi dan Pengujian Sistem**

Requirement: FR-01 sampai FR-12 dan NFR-01 sampai NFR-06.

Langkah:
1. Jalankan fitur pengunjung dan admin secara terintegrasi.
2. Periksa kesesuaian data antara katalog, detail buku, dan halaman pengelolaan.
3. Tinjau hasil pengujian Black Box.
4. Tinjau hasil pengujian White Box.
5. Catat kesalahan dan perbaikan yang masih diperlukan.

Hasil yang diharapkan: Fitur yang telah dikembangkan dapat dijalankan secara terintegrasi dan hasil pengujiannya terdokumentasi.

## 5. Lembar Hasil Evaluasi

Status diisi setelah demonstrasi dilaksanakan.

| Sprint | Skenario | Status | Catatan |
|---|---|---|---|
| Sprint 1 | Review dokumen dan rancangan | Belum ditinjau | - |
| Sprint 2 | DEMO-01 | Belum diuji | - |
| Sprint 2 | DEMO-02 | Belum diuji | - |
| Sprint 2 | DEMO-03 | Belum diuji | - |
| Sprint 2 | DEMO-04 | Belum diuji | - |
| Sprint 2 | DEMO-05 | Belum diuji | - |
| Sprint 3 | DEMO-06 | Belum diuji | - |
| Sprint 3 | DEMO-07 | Belum diuji | - |
| Sprint 3 | DEMO-08 | Belum diuji | - |
| Sprint 3 | DEMO-09 | Belum diuji | - |
| Sprint 3 | DEMO-10 | Belum diuji | - |
| Sprint 3 | DEMO-11 | Belum diuji | - |
| Sprint 4 | DEMO-12 | Belum diuji | - |
| Sprint 4 | DEMO-13 | Belum diuji | - |
| Sprint 4 | DEMO-14 | Belum diuji | - |

Pilihan status evaluasi:
- Belum diuji / Belum ditinjau
- Lulus / Disetujui
- Tidak lulus / Perlu revisi
- Tertunda

## 6. Catatan Sprint Review

- **Sprint:** Belum diisi
- **Tanggal pelaksanaan:** Belum diisi
- **Peserta:** Belum diisi
- **Hasil pengembangan yang ditinjau:** Belum diisi
- **Masukan Product Owner:** Belum diisi
- **Masukan pemangku kepentingan:** Belum diisi
- **Kendala atau kekurangan:** Belum diisi
- **Perbaikan yang diperlukan:** Belum diisi
- **Keputusan dan tindak lanjut:** Belum diisi

Catatan ini diisi berdasarkan hasil Sprint Review yang benar-benar dilaksanakan.

## 7. Ketentuan Penerimaan

1. Product Owner memeriksa hasil pengembangan berdasarkan kebutuhan sistem dan acceptance criteria yang telah disepakati.
2. Fitur dinyatakan diterima apabila memenuhi seluruh kriteria penerimaan wajib.
3. Fitur yang belum memenuhi kriteria penerimaan dicatat untuk diperbaiki.
4. Hasil Sprint Review digunakan sebagai masukan untuk memperbarui Product Backlog.
5. Keputusan penerimaan dan tindak lanjut didokumentasikan berdasarkan hasil evaluasi bersama tim.