# FINDLIB (Find Library)

## Sistem Informasi Ketersediaan Buku Ruang Baca FMIPA Universitas Halu Oleo

FINDLIB (Find Library) merupakan proyek pengembangan aplikasi berbasis website yang bertujuan menyediakan informasi mengenai ketersediaan buku di Ruang Baca Fakultas Matematika dan Ilmu Pengetahuan Alam (FMIPA), Universitas Halu Oleo.

Website ini dirancang untuk membantu pengunjung menemukan informasi buku secara lebih mudah melalui katalog digital. Informasi yang disediakan meliputi judul buku, penulis, kategori, lokasi rak, jumlah stok, dan status ketersediaan.

FINDLIB merupakan sistem informasi ketersediaan buku, bukan sistem peminjaman maupun reservasi buku.

## 1. Latar Belakang

Informasi mengenai koleksi dan ketersediaan buku merupakan salah satu kebutuhan pengguna ruang baca. Untuk mempermudah akses terhadap informasi tersebut, dikembangkan FINDLIB sebagai aplikasi berbasis website.

Melalui FINDLIB, pengunjung dapat mencari informasi buku tanpa harus melakukan login. Sementara itu, admin memiliki akses untuk mengelola data buku dan informasi pendukung agar katalog tetap sesuai dengan data yang tercatat dalam sistem.

Kondisi pengelolaan buku yang sebenarnya di Ruang Baca FMIPA Universitas Halu Oleo masih perlu dikonfirmasi melalui data pendukung yang tersedia.

## 2. Tujuan Pengembangan

Tujuan pengembangan FINDLIB adalah:

1. Menyediakan katalog buku berbasis website.
2. Mempermudah pengunjung mencari informasi buku.
3. Menampilkan informasi lokasi rak dan ketersediaan buku.
4. Membantu admin mengelola data koleksi buku.
5. Menyediakan informasi jumlah stok.
6. Mendukung pengelolaan informasi buku secara terstruktur.

## 3. Aktor Sistem

FINDLIB memiliki dua aktor utama.

### A. Pengunjung

Pengunjung merupakan pengguna umum yang dapat mengakses katalog buku tanpa melakukan login.

Hak akses pengunjung:

- Melihat daftar buku.
- Mencari buku berdasarkan informasi yang tersedia.
- Melihat detail buku.
- Melihat kategori dan lokasi rak.
- Melihat jumlah stok dan status ketersediaan.

### B. Admin

Admin merupakan pengguna yang memiliki akun dan hak akses untuk mengelola data sistem.

Hak akses admin:

- Login ke sistem.
- Mengakses dashboard admin.
- Menambah, melihat, mengubah, dan menghapus data buku.
- Mengelola jumlah stok buku.
- Mengelola kategori buku.
- Mengelola lokasi rak.
- Mengelola akun admin.
- Logout dari sistem.

## 4. Fitur Utama

### Fitur Pengunjung

| No. | Fitur | Deskripsi |
|---|---|---|
| 1 | Katalog Buku | Menampilkan halaman beranda dan daftar koleksi buku yang dapat diakses tanpa harus login. |
| 2 | Pencarian Buku | Memudahkan pencarian buku berdasarkan judul, penulis, atau kategori. |
| 3 | Detail Buku | Menampilkan informasi lengkap buku, meliputi No. Katalog, judul, penulis, penerbit, tahun terbit, dan kategori. |
| 4 | Lokasi Rak | Menampilkan kode dan nama lokasi rak penyimpanan buku. |
| 5 | Ketersediaan Buku | Menampilkan jumlah stok aktual dan status ketersediaan buku (Tersedia/Tidak Tersedia). |
| 6 | Antarmuka Responsif | Menyediakan tampilan website yang responsif dan mudah digunakan melalui HP maupun laptop. |
| 7 | Informasi Pembaruan | Menampilkan informasi waktu terakhir pembaruan status ketersediaan buku. |

### Fitur Admin

| No. | Fitur | Deskripsi |
|---|---|---|
| 1 | Login Admin | Memungkinkan admin masuk ke halaman dashboard pengelolaan menggunakan akun yang telah terdaftar. |
| 2 | Dashboard | Menampilkan ringkasan statistik jumlah koleksi buku pada dashboard pengelolaan. |
| 3 | Pengelolaan Data Buku | Memungkinkan admin menambahkan, mengubah, dan menghapus data buku (CRUD). |
| 4 | Pengelolaan Stok Buku | Memungkinkan admin memperbarui jumlah stok buku dengan perubahan status ketersediaan secara otomatis. |
| 5 | Pengelolaan Kategori dan Lokasi Rak | Memungkinkan admin mengelola data kategori buku serta kode lokasi rak penyimpanan. |
| 6 | Pengelolaan Akun Admin | Memungkinkan admin mengelola data akun admin atau pengelola ruang baca. |
| 7 | Data Koleksi dan Riwayat Stok | Memungkinkan admin melihat seluruh data koleksi buku serta riwayat pembaruan stok. |
| 8 | Logout | Memungkinkan admin keluar dari sistem untuk mengakhiri sesi pengelololaan. |

## 5. Batasan Sistem

FINDLIB memiliki batasan sebagai berikut:

1. Sistem berbasis website.
2. Sistem hanya memiliki dua aktor, yaitu Pengunjung dan Admin.
3. Pengunjung tidak memerlukan akun untuk melihat katalog.
4. Fitur pengelolaan hanya dapat diakses oleh admin yang telah login.
5. FINDLIB tidak menyediakan peminjaman buku.
6. FINDLIB tidak menyediakan reservasi buku.
7. FINDLIB tidak menyediakan pembayaran atau transaksi.
8. Informasi ketersediaan berasal dari data stok yang dikelola admin.
9. Sistem tidak menjamin kondisi fisik buku secara real-time tanpa pembaruan data oleh admin.

## 6. Teknologi yang Digunakan
| Teknologi          | Fungsi                                      |
| ------------------ | ------------------------------------------- |
| PHP Native         | Pemrosesan aplikasi pada sisi server        |
| MySQL              | Penyimpanan dan pengelolaan data aplikasi   |
| HTML               | Struktur halaman website                    |
| CSS                | Tampilan dan tata letak website             |
| JavaScript         | Interaksi pada halaman                      |

| Laragon            | Lingkungan pengembangan lokal               |
| Visual Studio Code | Editor kode                                 |
| Google Chrome      | Menjalankan dan menguji aplikasi web        |
| Claude AI          | Membantu mengarahkan penulisan kode program |
| Figma              | Membuat rancangan antarmuka dan wireframe   |
| Git                | Pengelolaan versi kode                      |
| GitHub             | Repository dan kolaborasi tim               |

## 7. Metode Pengembangan

Pengembangan FINDLIB menggunakan metode Agile dengan kerangka kerja Scrum.

Durasi proyek direncanakan selama empat minggu dengan pembagian empat Sprint, masing-masing selama satu minggu.

Kegiatan Scrum yang direncanakan meliputi:

- Sprint Planning.
- Pelaksanaan Sprint.
- Daily Scrum.
- Sprint Review.
- Sprint Retrospective.

## 8. Tim Pengembang

| Nama | Peran |
|---|---|
| Ranti Kurniasih | Scrum Master |
| Kallyn Renanda Putri | Product Owner |
| Chintia Andana Lestari | Developer 1 |
| Dwi Anggara | Developer 2 |
| Nadya Trialika | Developer 3 |

## 9. Struktur Repository

```text
FindLib/
├── admin/
│   ├── akun/
│   ├── buku/
│   ├── kategori/
│   └── rak/
├── assets/
│   ├── css/
│   ├── images/
│   └── js/
├── config/
├── database/
├── docs/
│   ├── product-backlog.md
│   ├── requirements.md
│   └── sprint-review.md
├── includes/
├── .gitignore
└── README.md
```

## 10. Cara Menjalankan Aplikasi

Berikut langkah-langkah menjalankan aplikasi FINDLIB menggunakan Laragon.

1. Instal Laragon, Visual Studio Code, dan Git.
2. Buka folder `C:\laragon\www` pada komputer.
3. Buka Git Bash pada folder tersebut.
4. Clone repository FINDLIB menggunakan perintah:

   ```bash
   git clone https://github.com/RantiKurniasih/FindLib.git
   ```

5. Buka Laragon dan klik **Start All** untuk menjalankan Apache dan MySQL.
6. Buka folder `FindLib` menggunakan Visual Studio Code.
7. Buka phpMyAdmin atau aplikasi pengelola database MySQL yang tersedia.
8. Buat database sesuai konfigurasi proyek FINDLIB.
9. Impor file SQL yang disediakan tim pengembang.
10. Sesuaikan konfigurasi koneksi database pada file PHP yang digunakan proyek.
11. Pastikan layanan Apache dan MySQL berjalan.
12. Buka Google Chrome dan masukkan alamat berikut:

   ```text
   http://localhost/FindLib/
   ```

13. Jika seluruh konfigurasi benar dan kode aplikasi sudah tersedia, halaman utama FINDLIB akan ditampilkan.

## 11. Dokumentasi Produk

Dokumentasi kebutuhan dan pengembangan produk tersedia pada:

- [Product Backlog](docs/product-backlog.md)
- [Software Requirements](docs/requirements.md)
- [Sprint Review](docs/sprint-review.md)

## 12. Status Pengembangan

FINDLIB merupakan proyek Rekayasa Perangkat Lunak (RPL) yang dikembangkan menggunakan metode Agile dengan kerangka kerja Scrum selama empat minggu.

Pengembangan sistem dilakukan secara bertahap berdasarkan Product Backlog dan Sprint Planning yang telah disusun oleh tim.

Setiap fitur dikembangkan, diuji, dan dievaluasi melalui kegiatan Sprint Review untuk memastikan kesesuaiannya dengan kebutuhan pengguna.

**Status proyek:** Dalam pengembangan.

**Status penyelesaian fitur:** Perlu dikonfirmasi berdasarkan perkembangan terbaru proyek.

## 13. Informasi Proyek

- Nama proyek: FINDLIB (Find Library)
- Institusi: Universitas Halu Oleo
- Fakultas: Matematika dan Ilmu Pengetahuan Alam
- Metode pengembangan: Scrum
- Durasi rencana: 4 minggu
- Repository: https://github.com/RantiKurniasih/FindLib