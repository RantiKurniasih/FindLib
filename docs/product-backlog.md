# Product Backlog FINDLIB

## 1. Informasi Dokumen

- Nama proyek: FINDLIB (Find Library)
- Metode pengembangan: Scrum
- Durasi proyek: 4 minggu
- Jumlah Sprint yang direncanakan: 4
- Status dokumen: Draft untuk dikonfirmasi

## 2. Tujuan Product Backlog

Product Backlog digunakan sebagai daftar kebutuhan yang menjadi dasar pengembangan FINDLIB.

Dokumen ini bertujuan untuk:

1. Menjelaskan fitur yang perlu dikembangkan.
2. Menentukan prioritas kebutuhan pengguna.
3. Membantu tim memilih pekerjaan dalam Sprint Planning.
4. Menentukan acceptance criteria setiap fitur.
5. Mendukung evaluasi hasil pada Sprint Review.

## 3. Daftar Product Backlog

| ID | Product Backlog Item | Prioritas | Target Sprint | Status |
|---|---|---|---|---|
| PB-01 | Beranda dan katalog buku tanpa login | Tinggi | 1–2 | Belum diverifikasi |
| PB-02 | Pencarian buku berdasarkan judul, penulis, dan kategori | Tinggi | 2 | Belum diverifikasi |
| PB-03 | Detail buku, lokasi rak, jumlah stok, status ketersediaan, dan waktu pembaruan terakhir | Tinggi | 2 | Belum diverifikasi |
| PB-04 | Login, logout, dan dashboard admin | Tinggi | 2 | Belum diverifikasi |
| PB-05 | CRUD data buku dan pembaruan stok otomatis | Tinggi | 3 | Belum diverifikasi |
| PB-06 | Pengelolaan kategori, lokasi rak, akun admin, dan riwayat stok | Sedang | 3–4 | Belum diverifikasi |


## 4. Rincian Product Backlog dan Acceptance Criteria

### PB-01 — Katalog dan Pencarian Buku

**User Story:**

Sebagai Pengunjung, saya ingin melihat katalog dan mencari buku agar dapat menemukan informasi buku yang saya butuhkan tanpa melakukan login.

**Prioritas:** Tinggi

**Acceptance Criteria:**

- AC-01: Pengunjung dapat membuka halaman beranda tanpa login.
- AC-02: Pengunjung dapat membuka halaman katalog tanpa login.
- AC-03: Sistem menampilkan daftar koleksi buku yang tersimpan dalam database.
- AC-04: Sistem menampilkan informasi utama buku pada katalog.
- AC-05: Pengunjung dapat memilih buku untuk melihat detailnya.
- AC-06: Tampilan katalog dapat digunakan melalui HP maupun laptop.
- AC-07: Pengunjung tidak dapat mengakses halaman pengelolaan admin.

**Kriteria penerimaan:** Fitur diterima apabila seluruh acceptance criteria di atas berhasil diperagakan dan diverifikasi.

### PB-02 — Detail dan Ketersediaan Buku

**User Story:**

Sebagai Pengunjung, saya ingin melihat detail buku, lokasi rak, jumlah stok, status ketersediaan, dan waktu pembaruan agar dapat mengetahui informasi buku sebelum mengunjungi ruang baca.

**Prioritas:** Tinggi

**Acceptance Criteria:**

- AC-01: Sistem menampilkan detail buku yang dipilih.
- AC-02: Detail buku menampilkan judul, penulis, dan kategori.
- AC-03: Sistem menampilkan informasi lokasi rak.
- AC-04: Sistem menampilkan jumlah stok buku.
- AC-05: Sistem menampilkan status "Tersedia" jika stok lebih dari 0.
- AC-06: Sistem menampilkan status "Tidak Tersedia" jika stok sama dengan 0.
- AC-07: Sistem menampilkan waktu terakhir pembaruan data buku.
- AC-08: Informasi yang ditampilkan sesuai dengan data yang tersimpan dalam database.

**Kriteria penerimaan:** Fitur diterima apabila informasi buku ditampilkan dengan benar, termasuk saat stok bernilai nol.

### PB-03 — Login, Logout, dan Dashboard Admin

**User Story:**

Sebagai Admin, saya ingin login ke sistem dan mengakses dashboard agar dapat mengelola informasi buku secara aman.

**Prioritas:** Must Have

**Acceptance Criteria:**

- AC-01: Sistem menyediakan halaman login admin.
- AC-02: Admin dapat login menggunakan kredensial yang valid.
- AC-03: Sistem menolak login menggunakan kredensial yang tidak valid.
- AC-04: Setelah login berhasil, admin diarahkan ke dashboard.
- AC-05: Dashboard menampilkan ringkasan data koleksi buku.
- AC-06: Pengguna yang belum login tidak dapat mengakses halaman pengelolaan admin.
- AC-07: Admin dapat logout.
- AC-08: Setelah logout, sesi admin berakhir dan halaman pengelolaan tidak dapat diakses tanpa login kembali.

**Kriteria penerimaan:** Fitur diterima apabila autentikasi, pembatasan akses, dashboard, dan logout bekerja sesuai ketentuan.

### PB-04 — Manajemen Buku dan Stok

**User Story:**

Sebagai Admin, saya ingin mengelola data buku dan jumlah stok agar informasi pada katalog tetap sesuai dengan data yang tercatat.

**Prioritas:** Must Have

**Acceptance Criteria:**

- AC-01: Admin dapat menambahkan data buku baru.
- AC-02: Admin dapat melihat daftar buku yang dikelola.
- AC-03: Admin dapat mengubah informasi buku.
- AC-04: Admin dapat menghapus data buku setelah melakukan konfirmasi.
- AC-05: Admin dapat memperbarui jumlah stok buku.
- AC-06: Sistem menolak jumlah stok negatif.
- AC-07: Perubahan data yang berhasil disimpan dapat dilihat kembali.
- AC-08: Perubahan stok memengaruhi status ketersediaan buku pada katalog.
- AC-09: Sistem menampilkan pesan apabila penyimpanan gagal.
- AC-10: Pengunjung tidak dapat melakukan perubahan data buku atau stok.

**Kriteria penerimaan:** Fitur diterima apabila proses tambah, lihat, ubah, hapus, dan pembaruan stok dapat dilakukan dengan benar.

### PB-05 — Manajemen Kategori dan Rak

**User Story:**

Sebagai Admin, saya ingin mengelola kategori dan lokasi rak agar koleksi buku dapat dikelompokkan dan ditemukan dengan lebih mudah.

**Prioritas:** Must Have

**Acceptance Criteria:**

- AC-01: Admin dapat melihat daftar kategori buku.
- AC-02: Admin dapat menambahkan, mengubah, dan menghapus kategori.
- AC-03: Admin dapat melihat daftar rak.
- AC-04: Admin dapat menambahkan, mengubah, dan menghapus data rak.
- AC-05: Admin dapat menghubungkan buku dengan kategori dan rak.
- AC-06: Informasi kategori dan rak yang tersimpan ditampilkan pada detail buku.
- AC-07: Sistem mencegah penghapusan kategori atau rak yang masih digunakan tanpa penanganan relasi data yang sesuai.
- AC-08: Pengunjung tidak dapat mengubah kategori maupun rak.

**Kriteria penerimaan:** Fitur diterima apabila pengelolaan kategori dan rak berfungsi tanpa merusak keterkaitan data buku.

### PB-06 — Manajemen Akun Admin dan Riwayat Perubahan Stok

**User Story:**

Sebagai Admin, saya ingin mengelola akun administrator dan melihat riwayat perubahan stok agar akses pengelolaan serta perubahan data dapat dipantau.

**Prioritas:** Should Have

**Acceptance Criteria:**

- AC-01: Admin yang memiliki hak pengelolaan akun dapat melihat daftar akun admin.
- AC-02: Admin yang berwenang dapat menambahkan akun admin.
- AC-03: Admin yang berwenang dapat memperbarui data akun admin.
- AC-04: Sistem mencegah penggunaan identitas login yang sama apabila identitas tersebut harus unik.
- AC-05: Kata sandi akun disimpan secara aman menggunakan hashing.
- AC-06: Sistem mencatat perubahan stok yang berhasil dilakukan.
- AC-07: Riwayat stok menampilkan buku, stok sebelum perubahan, stok setelah perubahan, waktu perubahan, dan admin yang melakukan perubahan.
- AC-08: Admin dapat melihat riwayat perubahan stok.
- AC-09: Pengunjung tidak dapat mengakses data akun admin maupun riwayat perubahan stok.

**Perlu dikonfirmasi:** Aturan penghapusan atau penonaktifan akun admin dan pembagian hak pengelolaan akun.

**Kriteria penerimaan:** Fitur diterima apabila pengelolaan akun sesuai kewenangan dan perubahan stok tercatat serta dapat ditampilkan.

## 5. Dasar Penentuan Prioritas

Prioritas ditentukan berdasarkan:

1. Kebutuhan utama pengunjung untuk mencari dan melihat ketersediaan buku.
2. Kebutuhan admin untuk menjaga keakuratan informasi.
3. Ketergantungan antarfitur.
4. Keterbatasan durasi pengembangan selama empat minggu.
5. Nilai manfaat fitur terhadap tujuan utama FINDLIB.

Fitur katalog, detail buku, login admin, dan pengelolaan data dasar menjadi fokus utama.

## 6. Rencana Pengembangan per Sprint

### Sprint 1 — Analisis Kebutuhan dan Perancangan

Kegiatan:
- Melaksanakan kickoff proyek.
- Menyusun kebutuhan sistem.
- Menyusun Product Backlog.
- Membuat rancangan UML dan ERD.
- Membuat wireframe antarmuka FINDLIB.

**Target hasil:** Dokumen kebutuhan dan rancangan sistem FINDLIB.

### Sprint 2 — Implementasi Fitur Pengunjung dan Login Admin

Kegiatan:
- Mengembangkan beranda dan katalog buku.
- Mengembangkan fitur pencarian buku.
- Mengembangkan halaman detail dan ketersediaan buku.
- Mengembangkan fitur login admin.

**Target hasil:** Fitur katalog, pencarian, detail buku, dan autentikasi dasar.

### Sprint 3 — Implementasi Pengelolaan Koleksi Buku

Kegiatan:
- Mengembangkan dashboard admin.
- Mengembangkan fitur tambah, lihat, ubah, dan hapus data buku.
- Mengembangkan pengelolaan kategori buku.
- Mengembangkan pengelolaan lokasi rak.
- Mengembangkan fitur pembaruan stok dan status ketersediaan otomatis.

**Target hasil:** Fitur pengelolaan koleksi buku yang terintegrasi.

### Sprint 4 — Integrasi, Pengujian, dan Penyempurnaan

Kegiatan:
- Mengembangkan riwayat perubahan stok.
- Melakukan integrasi seluruh fitur sistem.
- Melakukan perbaikan berdasarkan hasil evaluasi.
- Melakukan Black Box Testing.
- Melakukan White Box Testing.
- Menyusun dokumentasi hasil pengujian.

**Target hasil:** Prototipe FINDLIB yang telah diuji dan laporan hasil pengujian.

**Catatan:** Status penyelesaian kegiatan setiap Sprint perlu dikonfirmasi berdasarkan hasil implementasi dan pengujian sebenarnya.

## 7. Definition of Done

Suatu Product Backlog Item dapat dinyatakan selesai apabila:

1. Fitur telah diimplementasikan.
2. Fitur terintegrasi dengan komponen yang diperlukan.
3. Seluruh acceptance criteria terpenuhi.
4. Pengujian yang relevan telah dilakukan.
5. Tidak terdapat masalah kritis yang menghalangi penggunaan fitur.
6. Perubahan kode telah ditinjau dan digabungkan melalui alur GitHub yang disepakati.
7. Product Owner telah memeriksa kesesuaian fitur dengan kebutuhan.

## 8. Pengelolaan Perubahan Backlog

Product Owner dapat memperjelas, menambah, mengubah, atau mengurutkan kembali Product Backlog berdasarkan kebutuhan produk.

Perubahan yang memengaruhi Sprint berjalan perlu dibahas bersama Scrum Master dan tim pengembang agar tidak mengganggu Sprint Goal.

## 9. Hal yang Perlu Dikonfirmasi

- Kesesuaian kode PB-01 sampai PB-06 dengan laporan asli.
- Pembagian fitur pada setiap Sprint.
- Estimasi pekerjaan oleh tim pengembang.
- Aturan pengelolaan akun admin.
- Ketentuan penghapusan data yang memiliki relasi.
- Rincian data buku yang wajib disimpan.
- Ketersediaan data awal koleksi buku.