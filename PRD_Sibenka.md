# PRD: Sibenka (Sistem Inventaris Bengkel Skagata)

## 1. Tujuan & Latar Belakang

**Nama Proyek:** Sistem Inventaris Bengkel Skagata disingkat Sibenka (Berbasis Web).
**Tujuan Utama:** Mendigitalisasi proses pencatatan barang, sirkulasi peminjaman alat praktik, dan penyusunan Rencana Anggaran Biaya (RAB) pengadaan di lingkungan bengkel kejuruan SMKN 3 Yogyakarta. Sistem ini dirancang untuk mencegah kehilangan alat, mempercepat pelaporan, dan menertibkan administrasi peminjaman.

## 2. Definisi Peran & Hak Akses (User Roles)

Sistem memiliki 3 level pengguna:

### Super Admin (Waka Sarpras):

- Melihat dashboard analitik (grafik aset, kondisi barang).
- Meninjau dan menyetujui (Approve/Reject) draf RAB Pengadaan dari Toolman.
- Mengakses dan mengunduh laporan mutasi aset & konsumsi bahan.
- Mengelola master data jurusan/bengkel dan akun Toolman.
- Mengubah profil dan password akun.

### Admin Bengkel (Toolman):

- Mengelola master data barang (CRUD alat inventaris & bahan habis pakai).
- Memproses tiket peminjaman (Approve/Reject request siswa/guru).
- Melakukan pengecekan fisik saat pengembalian barang dan mengubah status kondisi.
- Mengajukan draf RAB secara otomatis dari stok limit/rusak.
- Melihat riwayat transaksi jurusannya.
- Melakukan Approval registrasi peminjam baru dan menangguhkan (Suspend) akun bermasalah.
- Mengubah profil dan password.

### Peminjam (Siswa & Guru):

- Mendaftar akun (menunggu approval Toolman).
- Melihat katalog barang yang tersedia.
- Membuat tiket pengajuan peminjaman (alat dan/atau bahan).
- Mengajukan pengembalian.
- Memantau status riwayat peminjaman (Tiket Saya).
- Mengubah profil dan password.

## 3. Aturan Bisnis Sistem (Business Rules)

- **Pencatatan Barang (Quantity-Based):** Barang dicatat berdasarkan jumlah (quantity), bukan nomor seri unik. Jika ada 5 Router, direkam sebagai 1 entitas barang.
- **Aturan Peminjaman:**
    - Tidak ada batasan jumlah maksimal barang yang boleh dipinjam dalam satu waktu.
    - Alat wajib dikembalikan pada hari yang sama.
    - Bahan Habis Pakai (BHP) tidak perlu dikembalikan. Jika tiket di-approve, stok BHP otomatis berkurang secara permanen.
- **Sanksi (Penalti):** Jika siswa merusak atau menghilangkan alat, atau sering terlambat, Toolman akan menangguhkan (Suspend) akun siswa. Urusan ganti rugi diselesaikan di luar sistem aplikasi. Siswa yang di-suspend tidak bisa membuat pengajuan baru.
- **Alur Pengadaan & Stok:**
    - Persetujuan pengadaan (RAB) oleh Waka Sarpras tidak otomatis menambah stok barang di sistem. Output RAB ini difungsikan sebagai laporan untuk sistem pemerintah.
    - Penambahan stok secara fisik ke dalam aplikasi dilakukan secara manual oleh Toolman saat barang benar-benar sudah tiba di bengkel.
- **Notifikasi:** Tidak ada integrasi email/WhatsApp. Peringatan keterlambatan, low-stock, dan tiket masuk hanya ditampilkan secara visual (badge/alert) di dalam aplikasi (In-App Notification).

## 4. Manajemen Status (State Management)

### Status Barang:

- **Tersedia:** Barang bisa dipinjam (stok baik > 0).
- **Rusak:** Barang tidak bisa dipinjam / masuk antrean perbaikan (stok rusak).
- **Hilang:** Barang hilang akan mengurangi stok total secara permanen, sama seperti BHP.

### Status Tiket Peminjaman:

- **Pending:** Peminjam sudah request, menunggu persetujuan Toolman.
- **Active:** Disetujui Toolman, barang sedang dibawa peminjam.
- **Terlambat:** Batas waktu peminjaman telah habis, barang belum dikembalikan.
- **Menunggu Pengecekan:** Toolman mengecek fisik barang yang dikembalikan.
- **Selesai:** Barang inventaris telah dikembalikan dan di-cek, atau request BHP telah disetujui.
- **Ditolak:** Toolman menolak pengajuan.

### Status Pengguna (Peminjam):

- **Menunggu Acc:** Akun baru dibuat, belum bisa login/meminjam.
- **Aktif:** Bisa melakukan aktivitas normal.
- **Suspend:** Akun ditangguhkan karena pelanggaran, akses pengajuan ditutup.

### Status Pengadaan (RAB):

- **Draft:** Disimpan sementara oleh Toolman, belum dikirim ke Waka.
- **Pending:** Menunggu review Waka Sarpras.
- **Revisi:** Dikembalikan oleh Waka untuk diperbaiki Toolman.
- **Approved:** Disetujui Waka Sarpras (Siap dicetak/dilaporkan).
- **Rejected:** Ditolak sepenuhnya.

## 5. Draf Entitas Database (High-Level)

Berdasarkan kebutuhan di atas, sistem akan membutuhkan tabel utama berikut:

- `users` (Data akun semua role, termasuk kolom role, status_akun).
- `bengkels` (Master data jurusan/bengkel).
- `barangs` (Master barang, meliputi kode_barang, nama, kategori: inventaris/bahan, stok_total, stok_tersedia, stok_dipinjam, stok_rusak).
- `peminjamans` (Header tiket transaksi, memuat user_id, tgl_pinjam, batas_kembali, status, keperluan).
- `detail_peminjamans` (Rincian barang di tiap tiket, memuat peminjaman_id, barang_id, jumlah, kondisi_kembali).
- `pengadaans` (Header pengajuan RAB, memuat judul, status_waka).
- `detail_pengadaans` (Isi item RAB yang diajukan Toolman).

> **Catatan Relasi:** Tabel `users` (untuk role Toolman dan Peminjam), `barangs`, dan `pengadaans` **wajib** memiliki foreign key `bengkel_id` agar data per jurusan terisolasi dengan aman.

## 6. Sitemap & Struktur Halaman

Sistem dibagi menjadi tiga area utama berdasarkan hak akses:

### Area Super Admin (Waka Sarpras) - Layout Sidebar

- **Dashboard:** Ringkasan total aset, grafik kondisi, alert barang.
- **Persetujuan Pengadaan:** Tabel pengajuan RAB dari seluruh bengkel (Approve/Revisi/Reject).
- **Laporan (Reporting):** Laporan Mutasi Aset & Laporan Konsumsi Bahan.
- **Master Data:** Data Bengkel/Jurusan & Manajemen Akun Toolman.
- **Pengaturan:** Profil & Ubah Password.

### Area Admin Bengkel (Toolman) - Layout Sidebar

- **Dashboard:** Ringkasan aktivitas (Barang dipinjam, Request baru, Stok menipis).
- **Manajemen Barang:** Katalog bengkel (CRUD Alat & Bahan).
- **Sirkulasi Peminjaman:** Antrean Acc request dari siswa/guru.
- **Sirkulasi Pengembalian:** Form cek fisik & pengubahan status kondisi barang.
- **Pengadaan Barang:** Form pembuatan RAB (Generate otomatis dari stok limit/rusak).
- **Manajemen Peminjam:** Tabel akun siswa/guru (Acc pendaftaran & Suspend akun).
- **Pengaturan:** Profil & Ubah Password.

### Area Peminjam (Siswa & Guru) - Layout Top Navbar

- **Katalog Barang (Beranda):** Grid daftar alat/bahan yang tersedia beserta fitur pencarian.
- **Form Pengajuan:** Form dinamis (Sistem pintar: kalender disembunyikan jika memilih bahan habis pakai).
- **Tiket Saya:** Riwayat peminjaman (Pending, Active, Selesai) dan fitur ajukan pengembalian.
- **Pengaturan:** Profil & Ubah Password.

## 7. Konsep UI & Design System

Sibenka menggunakan pendekatan desain **Clean & Functional Dashboard** dengan teknologi **Tailwind CSS**.

- **Warna Utama (Primary):** Hijau SMKN 3 Yogyakarta (menggunakan basis warna `Emerald-600` / `#059669` di Tailwind).
- **Warna Semantic (Status):**
    - Hijau (`Success`): Tersedia, Kondisi Baik, Selesai.
    - Kuning/Amber (`Warning`): Pending, Low Stock, Revisi.
    - Merah (`Danger`): Rusak, Hilang, Reject, Terlambat.
    - Biru (`Info`): Sedang dipinjam (Active).
- **Layouting:**
    - `Admin Layout:` Menggunakan _Sidebar Navigation_ (untuk Waka Sarpras & Toolman) karena butuh akses ke banyak sub-menu data padat.
    - `Peminjam Layout:` Menggunakan _Top Navigation/Navbar_ (untuk Siswa & Guru) agar lebih _mobile-friendly_ dan fokus pada katalog.
- **Tipografi:** Menggunakan font _sans-serif_ modern dan bersih (Inter atau Plus Jakarta Sans).
- **Bentuk Elemen:** Menggunakan sudut agak membulat (`rounded-lg` atau `rounded-xl`) untuk kesan modern.

## 8. Struktur File Frontend (Blade Views)

Untuk menjaga agar kode tetap rapi saat dikerjakan bersama tim, struktur folder `resources/views/` diatur sebagai berikut:

```text
resources/views/
├── auth/                       (Login & Register)
├── layouts/
│   ├── admin.blade.php         (Kerangka UI Waka & Toolman)
│   ├── peminjam.blade.php      (Kerangka UI Siswa/Guru)
│   └── auth.blade.php          (Kerangka UI Autentikasi)
├── superadmin/                 (Area Waka Sarpras)
│   ├── dashboard.blade.php
│   ├── pengadaan/
│   ├── laporan/
│   └── master/
├── toolman/                    (Area Admin Bengkel)
│   ├── dashboard.blade.php
│   ├── barang/
│   ├── sirkulasi/
│   ├── pengadaan/
│   └── users/
├── peminjam/                   (Area Siswa & Guru)
│   ├── katalog/
│   ├── pengajuan/
│   └── tiket/
└── profile/                    (Edit profil general)
```
