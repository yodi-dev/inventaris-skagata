<div align="center">

# 🛠️ SIBENKA (Sistem Inventaris Bengkel Skagata)

**Sistem Informasi Pengelolaan Inventaris, Sirkulasi Alat & Bahan Praktik, serta Pengadaan (RAB) Berbasis Web di Lingkungan Bengkel Kejuruan SMK Negeri 3 Yogyakarta**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)

</div>

---

## 📌 1. Latar Belakang & Tujuan

SMK Negeri 3 Yogyakarta (Skagata) memiliki beragam bengkel kejuruan (TKJ, TAV, TITL, TKR, TBSM, DKV, Tata Boga, dll.) dengan sirkulasi peminjaman alat praktik dan konsumsi bahan yang padat setiap hari.

**SIBENKA** dirancang untuk:
1. **Mencegah Kehilangan & Kerusakan Alat:** Mencatat sirkulasi peminjaman alat secara akuntabel dan menyediakan alur cek fisik kondisi barang saat pengembalian.
2. **Efisiensi Pengelolaan Bahan Habis Pakai (BHP):** Memonitor pemakaian bahan praktik siswa dan mendeteksi stok yang menipis secara *real-time*.
3. **Penyusunan RAB Pengadaan Cepat & Akurat:** Membantu Toolman mengusulkan Rencana Anggaran Biaya (RAB) secara otomatis berbasis stok limit dan alat rusak berat untuk disetujui Waka Sarpras.

---

## 👥 2. Hak Akses & Peran Pengguna (User Roles)

Sistem membagi akses ke dalam 3 tingkatan pengguna:

| Peran (Role) | Target Pengguna | Layout Navigasi | Tanggung Jawab Utama |
| :--- | :--- | :--- | :--- |
| **Super Admin** | Waka Sarpras | *Sidebar Navigation* | Monitoring analitik aset seluruh sekolah, verifikasi/persetujuan RAB pengadaan dari bengkel, rekap laporan mutasi & konsumsi bahan, manajemen master bengkel & akun toolman. |
| **Admin Bengkel** | Toolman Jurusan | *Sidebar Navigation* | Manajemen katalog barang bengkel (CRUD alat & BHP), verifikasi antrean peminjaman siswa/guru, cek fisik pengembalian, pengajuan draf RAB otomatis, manajemen peminjam (approval & suspend). |
| **Peminjam** | Siswa & Guru | *Top Navbar + Mobile Bottom Navigation* | Eksplorasi katalog alat/bahan (*mobile-first*), keranjang peminjaman multi-barang, formulir pengajuan cerdas, pemantauan status tiket sirkulasi. |

---

## 🚀 3. Fitur Utama Berdasarkan Peran

### A. Super Admin (Waka Sarpras)
- 📊 **Dashboard Analitik Eksekutif:** Statistik total aset, alat aktif dipinjam, barang rusak, stok kritis, dan grafik perbandingan antar jurusan.
- 📝 **Verifikasi RAB Pengadaan:** Antarmuka interaktif peninjauan usulan RAB dari tiap bengkel dengan opsi **Setujui (ACC)**, **Catatan Revisi**, atau **Tolak**, dilengkapi rincian spesifikasi teknis barang.
- 📈 **Laporan & Rekapitulasi:** Rekapitulasi mutasi aset dan laporan konsumsi bahan habis pakai yang siap cetak / ekspor.
- 🏢 **Master Data Terpusat:** Pengelolaan data jurusan/bengkel dan manajemen akun Toolman penanggung jawab.

### B. Admin Bengkel (Toolman)
- 📦 **Manajemen Master Barang (Quantity-Based):** Pendataan alat inventaris dan bahan habis pakai dengan informasi spesifikasi, stok total, stok tersedia, dan lokasi rak/lemari.
- 📋 **Sirkulasi Peminjaman (Antrean ACC):** Tinjau permohonan pinjam alat/bahan dari siswa dan guru secara cepat.
- 🔍 **Sirkulasi Pengembalian & Cek Fisik:** Form verifikasi pengembalian alat dengan pencatatan kondisi fisik (Baik / Rusak Ringan / Rusak Berat / Hilang).
- ⚡ **Pengajuan RAB Otomatis:** Tombol pintar *generate* draf RAB dari barang berstatus stok menipis dan alat rusak berat.
- 🛑 **Manajemen Peminjam:** Persetujuan registrasi akun siswa/guru baru serta penangguhan (*suspend*) akun yang melanggar tata tertib bengkel.

### C. Peminjam (Siswa & Guru)
- 📱 **Katalog Interaktif (Mobile-First):** Tampilan responsif dengan pencarian instan, filter chip kategori (*Alat Inventaris* vs *Bahan Habis Pakai*), serta indikator ketersediaan stok.
- 🛒 **Keranjang Peminjaman (Multi-Item):** Siswa dapat memilih beberapa barang sekaligus dalam satu transaksi peminjaman.
- 🧠 **Sistem Pintar Formulir Pengajuan:**
  - *Bahan Habis Pakai (BHP):* Input kalender/jadwal pengembalian **otomatis disembunyikan**, karena BHP tidak perlu dikembalikan.
  - *Alat Inventaris:* Wajib mencantumkan batas pengembalian pada hari yang sama.
- 🎟️ **Tiket Peminjaman Saya:** Pelacakan status tiket secara *real-time* (*Pending*, *Active / Sedang Dipinjam*, *Selesai*), dan tombol aksi ajukan pengembalian.

---

## ⚖️ 4. Aturan Bisnis Sistem (Business Rules)

> [!IMPORTANT]
> - **Quantity-Based Inventory:** Barang dicatat berdasarkan kuantitas fisik (misal: 5 Unit Router), bukan nomor seri satuan per unit.
> - **Aturan Pengembalian Alat:** Alat inventaris **wajib dikembalikan pada hari yang sama** sebelum jam bengkel berakhir (maksimal 15:30 WIB).
> - **Bahan Habis Pakai (BHP):** Bahan habis pakai (kabel, konektor, timah solder, dll.) **tidak perlu dikembalikan**. Begitu disetujui Toolman, stok otomatis berkurang secara permanen.
> - **Sanksi Penalti (*Suspend*):** Peminjam yang menghilangkan alat, merusak, atau terlambat mengembalikan akan di-*suspend* oleh Toolman dan tidak dapat membuat pengajuan baru hingga urusan diselesaikan di luar sistem.
> - **Alur Stok RAB:** Persetujuan RAB oleh Waka Sarpras tidak otomatis menambah stok aplikasi. Stok fisik ditambahkan secara manual oleh Toolman setelah barang fisik resmi diterima di bengkel.
> - **Notifikasi:** Notifikasi berjalan secara visual di dalam aplikasi (*In-App Badge & Toasts*), tanpa ketergantungan API pihak ketiga (Email/WhatsApp).

---

## 📂 5. Struktur Direktori Proyek

```text
inventaris-skagata/
├── app/
│   ├── Http/
│   │   └── Controllers/       # Controller logic aplikasi
│   └── Models/                # Eloquent models
├── resources/
│   ├── css/
│   │   └── app.css            # Konfigurasi Tailwind & typography Inter
│   ├── js/
│   │   ├── app.js             # Inisialisasi Alpine.js & Bootstrap
│   │   └── bootstrap.js       # Axios setup
│   └── views/
│       ├── auth/              # Halaman Login & Registrasi
│       ├── layouts/
│       │   ├── admin.blade.php      # Layout Sidebar (Super Admin & Toolman)
│       │   ├── peminjam.blade.php   # Layout Mobile-First (Siswa & Guru)
│       │   └── auth.blade.php       # Layout autentikasi
│       ├── superadmin/        # Modul Waka Sarpras (Dashboard, Pengadaan, Master, Laporan)
│       ├── toolman/           # Modul Admin Bengkel (Dashboard, Barang, Sirkulasi, RAB, Users)
│       ├── peminjam/          # Modul Siswa/Guru (Katalog, Pengajuan, Tiket Saya)
│       └── profile/           # Manajemen profil akun
├── routes/
│   ├── web.php                # Definisi route sistem Sibenka
│   └── auth.php               # Route autentikasi Breeze
├── PRD_Sibenka.md             # Dokumen Product Requirement Document
└── README.md                  # Dokumentasi teknis proyek
```

---

## 🛠️ 6. Teknologi yang Digunakan

- **Backend:** [PHP 8.2+](https://php.net) & [Laravel 12 Framework](https://laravel.com)
- **Frontend Engine:** [Blade Templating Engine](https://laravel.com/docs/blade)
- **CSS Framework:** [Tailwind CSS v3 / v4](https://tailwindcss.com) (Basis warna identitas hijau SMKN 3 Yk: `Emerald-600` / `#059669`)
- **Interaktivitas Klien:** [Alpine.js](https://alpinejs.dev) untuk interaktivitas reaktif, modal, drawer, dan state management lokal
- **Build Tool:** [Vite](https://vitejs.dev)
- **Database:** MySQL / SQLite
- **Lingkungan Lokal:** [Laravel Herd](https://herd.laravel.com) (Windows / macOS)

---

## 💻 7. Panduan Instalasi & Menjalankan Aplikasi

### Prasyarat:
- PHP >= 8.2
- Composer >= 2.x
- Node.js >= 18.x & NPM
- Laravel Herd (atau Laragon / XAMPP)

### Langkah-langkah:

1. **Clone Repository & Masuk ke Direktori:**
   ```bash
   git clone https://github.com/yodi-dev/inventaris-skagata.git
   cd inventaris-skagata
   ```

2. **Instal Dependensi PHP (Composer):**
   ```bash
   composer install
   ```

3. **Salin File Environment & Generate App Key:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Instal Dependensi Frontend (NPM):**
   ```bash
   npm install
   ```

5. **Migrasi Database & Seeder (Jika database sudah disiapkan):**
   ```bash
   php artisan migrate
   ```

6. **Jalankan Server Pengembang (Development):**
   - **Jika menggunakan Laravel Herd:**
     Aplikasi langsung dapat diakses pada browser di:
     `http://inventaris-skagata.test`
   - **Atau jalankan secara manual:**
     ```bash
     # Terminal 1: Menjalankan Vite asset bundler
     npm run dev

     # Terminal 2: Menjalankan server Laravel
     php artisan serve
     ```
     Buka [http://127.0.0.1:8000](http://127.0.0.1:8000) pada browser Anda.

---

## 🧭 8. Peta Route Utama Prototype

| Modul | URL Endpoint | Keterangan Halaman |
| :--- | :--- | :--- |
| **Login** | `/login` | Pintu masuk autentikasi pengguna |
| **Super Admin** | `/superadmin/dashboard` | Dashboard analitik Waka Sarpras |
| **Super Admin** | `/superadmin/pengadaan` | Verifikasi draf RAB Pengadaan (Interaktif) |
| **Toolman** | `/toolman/dashboard` | Dashboard operasional bengkel jurusan |
| **Toolman** | `/toolman/barang` | Manajemen master alat & bahan |
| **Toolman** | `/toolman/sirkulasi/peminjaman` | Antrean persetujuan pinjam siswa/guru |
| **Toolman** | `/toolman/pengadaan/create` | Form pengajuan RAB dari stok kritis |
| **Peminjam** | `/peminjam/katalog` | Katalog barang praktik (*Mobile-First* & Keranjang) |
| **Peminjam** | `/peminjam/tiket` | Tiket aktif & riwayat peminjaman |

---

## 📄 9. Lisensi & Hak Cipta

Dikembangkan untuk kebutuhan digitalisasi operasional bengkel kejuruan di **SMK Negeri 3 Yogyakarta**.  
Hak cipta dilindungi undang-undang &copy; 2026.
