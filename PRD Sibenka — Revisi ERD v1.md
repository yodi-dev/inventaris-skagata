# PRD: Sibenka (Sistem Inventaris Bengkel Skagata)

## 1. Tujuan & Latar Belakang

**Nama Proyek:** Sistem Inventaris Bengkel Skagata disingkat **Sibenka** (Berbasis Web).

**Tujuan Utama:** Mendigitalisasi proses pencatatan barang, sirkulasi peminjaman alat praktik, dan penyusunan Rencana Anggaran Biaya (RAB) pengadaan di lingkungan bengkel kejuruan SMKN 3 Yogyakarta.

Sistem dirancang untuk membantu mencegah kehilangan alat, mempermudah pemantauan kondisi dan ketersediaan barang, mempercepat pelaporan, serta menertibkan administrasi peminjaman dan pengadaan barang bengkel.

---

## 2. Definisi Peran & Hak Akses (User Roles)

Sistem memiliki 3 level pengguna utama:

### Super Admin (Waka Sarpras)

- Melihat dashboard analitik seluruh bengkel, termasuk data aset, kondisi barang, dan informasi stok.
- Meninjau pengajuan RAB dari seluruh bengkel.
- Memberikan keputusan terhadap pengajuan RAB berupa Approve, Revisi, atau Reject.
- Mengakses dan mengunduh laporan mutasi aset serta konsumsi bahan.
- Mengelola master data bengkel.
- Mengelola dan meng-assign akun Toolman ke bengkel.
- Mengubah profil dan password akun.

Waka Sarpras tidak terikat pada satu bengkel tertentu sehingga `bengkel_id` pada akun Waka Sarpras bernilai `NULL`.

### Admin Bengkel (Toolman)

- Terikat pada satu bengkel.
- Mengelola master data barang pada bengkel yang menjadi tanggung jawabnya.
- Mengelola alat inventaris dan Bahan Habis Pakai (BHP).
- Memproses tiket peminjaman dari siswa maupun guru.
- Melakukan Approve atau Reject terhadap tiket peminjaman.
- Melakukan pengecekan fisik ketika barang inventaris dikembalikan.
- Mencatat jumlah barang yang kembali dalam kondisi baik, rusak, atau hilang.
- Mengelola penambahan dan penyesuaian stok barang.
- Membuat dan mengajukan draft RAB pengadaan.
- Melihat riwayat transaksi pada bengkelnya.
- Melakukan approval registrasi peminjam baru.
- Menangguhkan (Suspend) akun peminjam yang bermasalah.
- Mengubah profil dan password.

Setiap Toolman wajib memiliki `bengkel_id`.

### Peminjam (Siswa & Guru)

- Mendaftar akun dan menunggu approval dari Toolman.
- Melihat katalog barang yang dapat dipinjam.
- Membuat tiket pengajuan peminjaman alat inventaris dan/atau BHP.
- Mengajukan pengembalian barang inventaris.
- Memantau status dan riwayat peminjaman melalui halaman Tiket Saya.
- Mengubah profil dan password.

Peminjam dibedakan menjadi:

#### Siswa

- Wajib terikat pada satu bengkel melalui `bengkel_id`.
- Hanya dapat melakukan peminjaman pada bengkel yang sesuai dengan akun siswa.

#### Guru

- Tidak terikat pada satu bengkel tertentu sehingga `bengkel_id` bernilai `NULL`.
- Dapat melakukan peminjaman pada bengkel yang berbeda.
- Bengkel tujuan peminjaman dicatat pada transaksi peminjaman.

---

## 3. Aturan Bisnis Sistem (Business Rules)

### 3.1 Master Bengkel

- Bengkel menjadi unit utama untuk mengisolasi data barang dan transaksi.
- Master bengkel dikelola oleh Waka Sarpras.
- Data bengkel minimal memiliki kode dan nama bengkel.
- Data jurusan tidak dibuat sebagai entitas terpisah karena identitas bengkel sudah direpresentasikan oleh kode dan nama bengkel.
- Satu bengkel dapat memiliki lebih dari satu Toolman.
- Satu Toolman hanya terikat pada satu bengkel.

### 3.2 Pengguna

- Seluruh akun disimpan dalam satu entitas `users`.
- Role utama terdiri dari `waka`, `toolman`, dan `peminjam`.
- Peminjam memiliki atribut tambahan `jenis_peminjam` dengan nilai `siswa` atau `guru`.
- Identitas siswa atau guru dapat disimpan melalui `nomor_identitas`, yang dapat merepresentasikan NIS atau NIP sesuai jenis peminjam.
- Nomor WhatsApp dapat disimpan sebagai data kontak pengguna, tetapi tidak digunakan untuk notifikasi otomatis.
- `bengkel_id` wajib untuk Toolman dan peminjam berjenis Siswa.
- `bengkel_id` bernilai `NULL` untuk Waka Sarpras dan peminjam berjenis Guru.

### 3.3 Pencatatan Barang

Barang dicatat menggunakan pendekatan **Quantity-Based**, bukan berdasarkan nomor seri individual.

Contoh: apabila terdapat 5 Router dengan jenis yang sama, sistem mencatatnya sebagai satu data barang dengan jumlah stok 5.

Barang dibedakan menjadi:

- **Inventaris:** barang yang harus dikembalikan setelah digunakan.
- **Bahan Habis Pakai (BHP):** barang yang tidak perlu dikembalikan setelah digunakan.

Setiap barang terikat pada satu bengkel.

Kode barang bersifat unik dalam satu bengkel. Bengkel berbeda diperbolehkan memiliki kode barang yang sama.

Stok barang terdiri dari:

- `stok_total`
- `stok_tersedia`
- `stok_dipinjam`
- `stok_rusak`

Untuk barang inventaris berlaku konsep:

`stok_total = stok_tersedia + stok_dipinjam + stok_rusak`

Barang juga memiliki `minimum_stok` sebagai batas untuk menentukan kondisi low-stock.

Low-stock tidak disimpan sebagai status khusus, melainkan ditentukan berdasarkan kondisi:

`stok_tersedia <= minimum_stok`

### 3.4 Aturan Peminjaman

- Tidak ada batasan jumlah maksimal jenis barang yang dapat dimasukkan ke dalam satu tiket peminjaman.
- Satu tiket dapat berisi barang Inventaris saja, BHP saja, atau kombinasi keduanya.
- Seluruh barang di dalam satu tiket harus berasal dari bengkel yang sama.
- Bengkel tempat transaksi dilakukan dicatat pada `peminjamans.bengkel_id`.
- Siswa hanya dapat melakukan peminjaman pada bengkel yang sesuai dengan `users.bengkel_id`.
- Guru dapat melakukan peminjaman pada bengkel yang berbeda karena akun Guru tidak terikat pada satu bengkel.
- Barang inventaris wajib dikembalikan pada hari yang sama.
- Batas pengembalian disimpan sebagai waktu/tanggal (`DATETIME`) pada transaksi peminjaman.
- Untuk tiket yang hanya berisi BHP, `batas_kembali` dapat bernilai `NULL`.

### 3.5 Peminjaman Barang Inventaris

Ketika peminjaman barang inventaris disetujui:

- `stok_tersedia` berkurang sesuai jumlah barang.
- `stok_dipinjam` bertambah sesuai jumlah barang.
- `stok_total` tidak berubah.

Ketika barang dikembalikan, Toolman melakukan pengecekan fisik dan mencatat jumlah berdasarkan kondisi:

- jumlah kembali baik;
- jumlah kembali rusak;
- jumlah hilang.

Untuk setiap detail barang inventaris yang telah selesai diperiksa berlaku:

`jumlah_baik + jumlah_rusak + jumlah_hilang = jumlah`

Barang yang kembali baik akan dikembalikan ke `stok_tersedia`.

Barang yang kembali rusak akan dipindahkan dari `stok_dipinjam` ke `stok_rusak`.

Barang yang hilang akan mengurangi `stok_dipinjam` sekaligus `stok_total` secara permanen.

### 3.6 Peminjaman Bahan Habis Pakai (BHP)

BHP tidak memiliki proses pengembalian.

Ketika tiket BHP disetujui:

- `stok_tersedia` berkurang.
- `stok_total` berkurang secara permanen.

Jika tiket hanya berisi BHP, transaksi dapat langsung berstatus **Selesai** setelah disetujui.

Jika tiket berisi kombinasi Inventaris dan BHP:

- stok BHP langsung berkurang ketika tiket disetujui;
- tiket tetap berstatus **Active** karena masih terdapat barang Inventaris yang harus dikembalikan;
- tiket dinyatakan selesai setelah seluruh barang Inventaris selesai diperiksa.

### 3.7 Sanksi (Penalti)

Jika peminjam merusak atau menghilangkan barang, atau melakukan pelanggaran seperti keterlambatan berulang, Toolman dapat menangguhkan akun peminjam.

Pengguna dengan status **Suspend** tidak dapat membuat pengajuan peminjaman baru.

Urusan ganti rugi atau penyelesaian administratif dilakukan di luar sistem Sibenka.

### 3.8 Pengadaan & RAB

- Toolman membuat draft RAB untuk bengkelnya.
- RAB dapat memuat barang yang sudah terdaftar pada master barang maupun barang baru yang belum ada dalam master barang.
- Toolman dapat menyusun RAB berdasarkan kebutuhan bengkel, termasuk kondisi stok minimum atau barang rusak.
- Draft RAB dikirim kepada Waka Sarpras untuk direview.
- Waka dapat memberikan keputusan Approved, Revisi, atau Rejected.
- Jika status Revisi, Waka memberikan catatan review yang digunakan Toolman untuk memperbaiki RAB.
- Persetujuan RAB tidak otomatis menambah stok barang.
- RAB berfungsi sebagai dokumen pengajuan/pelaporan pengadaan.
- Penambahan stok dilakukan oleh Toolman hanya ketika barang fisik benar-benar telah diterima di bengkel.

### 3.9 Riwayat Perubahan Stok

Setiap perubahan penting terhadap stok barang dicatat pada `stock_movements`.

`barangs` digunakan untuk menyimpan kondisi stok saat ini, sedangkan `stock_movements` digunakan sebagai riwayat mengapa stok berubah.

Jenis pergerakan stok minimal meliputi:

- `stok_masuk`
- `peminjaman`
- `pengembalian_baik`
- `pengembalian_rusak`
- `barang_hilang`
- `bhp_keluar`
- `perbaikan`
- `penyesuaian`

Setiap movement menyimpan barang, jumlah, pengguna yang memproses, jenis movement, serta referensi transaksi jika tersedia.

### 3.10 Notifikasi

Tidak terdapat integrasi notifikasi melalui email atau WhatsApp.

Peringatan sistem seperti:

- keterlambatan;
- stok menipis;
- tiket peminjaman baru;
- pengajuan RAB baru;

ditampilkan secara visual melalui badge, alert, atau indikator di dalam aplikasi.

---

## 4. Manajemen Status (State Management)

### 4.1 Kondisi Stok Barang

Barang tidak menggunakan satu status tunggal seperti Tersedia/Rusak/Hilang karena pencatatan menggunakan sistem quantity-based.

Satu jenis barang dapat secara bersamaan memiliki:

- stok tersedia;
- stok sedang dipinjam;
- stok rusak.

Contoh:

`10 Bor Tangan = 7 tersedia + 2 dipinjam + 1 rusak`

Barang hilang tidak disimpan sebagai status stok permanen. Kehilangan dicatat sebagai pergerakan stok yang mengurangi `stok_total`.

### 4.2 Status Tiket Peminjaman

- **Pending:** Peminjam telah membuat pengajuan dan menunggu keputusan Toolman.
- **Active:** Pengajuan disetujui dan terdapat barang Inventaris yang sedang dipinjam.
- **Terlambat:** Batas pengembalian telah terlewati dan barang Inventaris belum dikembalikan.
- **Menunggu Pengecekan:** Peminjam telah mengajukan pengembalian dan Toolman perlu melakukan pengecekan fisik.
- **Selesai:** Seluruh proses peminjaman telah selesai. Untuk tiket BHP-only, status ini dapat diberikan langsung setelah approval.
- **Ditolak:** Toolman menolak pengajuan peminjaman.

Alur utama Inventaris:

`Pending → Active → Menunggu Pengecekan → Selesai`

Jika melewati batas pengembalian:

`Active → Terlambat → Menunggu Pengecekan → Selesai`

Jika ditolak:

`Pending → Ditolak`

Untuk BHP-only:

`Pending → Selesai`

atau:

`Pending → Ditolak`

### 4.3 Status Pengguna Peminjam

- **Menunggu Acc:** Akun baru telah dibuat tetapi belum memperoleh approval.
- **Aktif:** Akun dapat melakukan aktivitas normal.
- **Suspend:** Akun ditangguhkan dan tidak dapat membuat pengajuan baru.

### 4.4 Status Pengadaan (RAB)

- **Draft:** Disimpan sementara oleh Toolman dan belum dikirim ke Waka.
- **Pending:** Telah diajukan dan sedang menunggu review Waka Sarpras.
- **Revisi:** Dikembalikan oleh Waka agar diperbaiki Toolman.
- **Approved:** Disetujui oleh Waka Sarpras.
- **Rejected:** Ditolak sepenuhnya oleh Waka Sarpras.

Alur utama:

`Draft → Pending → Approved`

atau:

`Draft → Pending → Revisi → Pending → Approved`

atau:

`Draft → Pending → Rejected`

---

## 5. Draf Entitas Database (ERD v1)

Sistem menggunakan delapan tabel utama.

### 5.1 `bengkels`

Master data bengkel.

Atribut utama:

- `id`
- `kode`
- `nama`
- `deskripsi` nullable
- `created_at`
- `updated_at`

Relasi utama:

- satu Bengkel memiliki banyak User;
- satu Bengkel memiliki banyak Barang;
- satu Bengkel memiliki banyak Peminjaman;
- satu Bengkel memiliki banyak Pengadaan.

---

### 5.2 `users`

Menyimpan seluruh akun sistem.

Atribut utama:

- `id`
- `bengkel_id` nullable
- `name`
- `email`
- `password`
- `role`
- `jenis_peminjam` nullable
- `nomor_identitas` nullable
- `nomor_wa` nullable
- `status`
- `created_at`
- `updated_at`

Nilai `role`:

- `waka`
- `toolman`
- `peminjam`

Nilai `jenis_peminjam`:

- `siswa`
- `guru`
- `NULL` untuk Waka dan Toolman

Aturan `bengkel_id`:

- Waka → `NULL`
- Toolman → wajib
- Siswa → wajib
- Guru → `NULL`

`nomor_identitas` dapat digunakan untuk menyimpan NIS siswa, NIP guru, atau identitas pegawai sesuai kebutuhan.

---

### 5.3 `barangs`

Master data barang pada setiap bengkel.

Atribut utama:

- `id`
- `bengkel_id`
- `kode_barang`
- `nama`
- `jenis_barang`
- `satuan`
- `stok_total`
- `stok_tersedia`
- `stok_dipinjam`
- `stok_rusak`
- `minimum_stok`
- `deskripsi` nullable
- `created_at`
- `updated_at`

Nilai `jenis_barang`:

- `inventaris`
- `bhp`

Kode barang unik berdasarkan kombinasi:

`bengkel_id + kode_barang`

---

### 5.4 `peminjamans`

Header transaksi peminjaman.

Atribut utama:

- `id`
- `user_id`
- `bengkel_id`
- `tanggal_pinjam`
- `batas_kembali` nullable
- `keperluan`
- `status`
- `diproses_oleh` nullable
- `diproses_pada` nullable
- `alasan_penolakan` nullable
- `created_at`
- `updated_at`

`user_id` mengarah kepada akun peminjam.

`bengkel_id` menunjukkan bengkel tempat transaksi dilakukan.

`diproses_oleh` mengarah kepada Toolman yang melakukan Approve atau Reject terhadap tiket.

---

### 5.5 `detail_peminjamans`

Menyimpan daftar barang di dalam satu tiket peminjaman.

Atribut utama:

- `id`
- `peminjaman_id`
- `barang_id`
- `jumlah`
- `jumlah_baik`
- `jumlah_rusak`
- `jumlah_hilang`
- `created_at`
- `updated_at`

Untuk barang Inventaris setelah pengecekan selesai:

`jumlah_baik + jumlah_rusak + jumlah_hilang = jumlah`

Untuk BHP, data kondisi pengembalian tidak digunakan karena BHP tidak dikembalikan.

---

### 5.6 `pengadaans`

Header pengajuan RAB.

Atribut utama:

- `id`
- `bengkel_id`
- `dibuat_oleh`
- `judul`
- `status`
- `catatan` nullable
- `catatan_review` nullable
- `diajukan_pada` nullable
- `direview_oleh` nullable
- `direview_pada` nullable
- `created_at`
- `updated_at`

`dibuat_oleh` mengarah kepada Toolman pembuat RAB.

`direview_oleh` mengarah kepada Waka Sarpras yang melakukan review.

---

### 5.7 `detail_pengadaans`

Menyimpan daftar item dalam satu RAB.

Atribut utama:

- `id`
- `pengadaan_id`
- `barang_id` nullable
- `nama_barang`
- `spesifikasi` nullable
- `jumlah`
- `satuan`
- `harga_satuan`
- `created_at`
- `updated_at`

`barang_id` dapat bernilai `NULL` karena RAB diperbolehkan memuat barang yang belum terdapat pada master barang.

Subtotal tidak harus disimpan karena dapat dihitung menggunakan:

`jumlah × harga_satuan`

Total RAB dapat dihitung dari seluruh subtotal item.

---

### 5.8 `stock_movements`

Menyimpan riwayat perubahan stok barang.

Atribut utama:

- `id`
- `barang_id`
- `user_id`
- `jenis`
- `jumlah`
- `referensi_tipe` nullable
- `referensi_id` nullable
- `keterangan` nullable
- `created_at`

Nilai `jenis` minimal:

- `stok_masuk`
- `peminjaman`
- `pengembalian_baik`
- `pengembalian_rusak`
- `barang_hilang`
- `bhp_keluar`
- `perbaikan`
- `penyesuaian`

`referensi_tipe` dan `referensi_id` digunakan untuk mengetahui transaksi yang menyebabkan perubahan stok, misalnya peminjaman atau pengadaan.

`stock_movements` berfungsi sebagai riwayat mutasi dan tidak menggantikan data stok saat ini pada tabel `barangs`.

---

## 6. Ringkasan Relasi Utama

Relasi utama ERD v1:

```text
BENGKELS
├── USERS
├── BARANGS
├── PEMINJAMANS
└── PENGADAANS

USERS
├── PEMINJAMANS
├── PENGADAANS
└── STOCK_MOVEMENTS

PEMINJAMANS
└── DETAIL_PEMINJAMANS
      └── BARANGS

PENGADAANS
└── DETAIL_PENGADAANS
      └── BARANGS (nullable)

BARANGS
└── STOCK_MOVEMENTS
```

Cardinality utama:

```text
bengkels 1 ─── N users
bengkels 1 ─── N barangs
bengkels 1 ─── N peminjamans
bengkels 1 ─── N pengadaans

users 1 ─── N peminjamans

peminjamans 1 ─── N detail_peminjamans
barangs 1 ─── N detail_peminjamans

pengadaans 1 ─── N detail_pengadaans
barangs 1 ─── N detail_pengadaans (optional dari sisi detail)

barangs 1 ─── N stock_movements
```

---

## 7. Sitemap & Struktur Halaman

Sistem dibagi menjadi tiga area utama berdasarkan hak akses.

### Area Super Admin (Waka Sarpras) — Layout Sidebar

- **Dashboard:** Ringkasan seluruh aset, kondisi barang, dan alert stok.
- **Persetujuan Pengadaan:** Daftar pengajuan RAB dari seluruh bengkel dengan aksi Approve, Revisi, atau Reject.
- **Laporan:** Laporan mutasi aset dan konsumsi BHP.
- **Master Data Bengkel:** CRUD data bengkel.
- **Manajemen Toolman:** CRUD akun Toolman dan assignment Toolman ke bengkel.
- **Pengaturan:** Profil dan ubah password.

### Area Admin Bengkel (Toolman) — Layout Sidebar

- **Dashboard:** Ringkasan aktivitas bengkel seperti barang dipinjam, request baru, dan stok menipis.
- **Manajemen Barang:** CRUD alat Inventaris dan BHP.
- **Sirkulasi Peminjaman:** Antrean pengajuan peminjaman dari siswa dan guru.
- **Sirkulasi Pengembalian:** Pengajuan pengembalian serta form pengecekan kondisi fisik barang.
- **Pengadaan Barang:** Pembuatan dan pengelolaan draft RAB.
- **Manajemen Peminjam:** Daftar akun siswa/guru, approval registrasi, dan Suspend akun.
- **Riwayat Stok/Mutasi:** Riwayat perubahan stok barang.
- **Pengaturan:** Profil dan ubah password.

### Area Peminjam (Siswa & Guru) — Layout Top Navbar

- **Katalog Barang:** Daftar alat Inventaris dan BHP yang tersedia.
- **Form Pengajuan:** Form pembuatan tiket peminjaman.
- **Tiket Saya:** Riwayat tiket dengan status Pending, Active, Terlambat, Menunggu Pengecekan, Selesai, atau Ditolak.
- **Pengaturan:** Profil dan ubah password.

Untuk Siswa, katalog dan peminjaman dibatasi pada bengkel yang terhubung dengan akun siswa.

Untuk Guru, pengguna dapat memilih atau mengakses bengkel tujuan sebelum melakukan peminjaman.

Pada tiket BHP-only, tidak diperlukan batas pengembalian.

Pada tiket yang memuat barang Inventaris, batas pengembalian wajib tersedia.

---

## 8. Konsep UI & Design System

Sibenka menggunakan pendekatan desain **Clean & Functional Dashboard** dengan teknologi **Tailwind CSS**.

### Warna Utama

Hijau SMKN 3 Yogyakarta dengan basis warna:

`Emerald-600 / #059669`

### Warna Semantic

- Hijau (`Success`): stok tersedia, kondisi baik, selesai, approved.
- Kuning/Amber (`Warning`): Pending, low-stock, revisi.
- Merah (`Danger`): rusak, hilang, rejected, terlambat, suspend.
- Biru (`Info`): sedang dipinjam / Active.

### Layout

**Admin Layout**

Digunakan oleh Waka Sarpras dan Toolman dengan Sidebar Navigation karena memiliki menu dan data administratif yang lebih padat.

**Peminjam Layout**

Digunakan oleh Siswa dan Guru dengan Top Navigation/Navbar agar lebih mobile-friendly dan berfokus pada katalog serta aktivitas peminjaman.

### Tipografi

Menggunakan font sans-serif modern seperti:

- Inter
- Plus Jakarta Sans

### Bentuk Elemen

Menggunakan rounded corner modern seperti:

- `rounded-lg`
- `rounded-xl`

dengan tampilan bersih, sederhana, dan mengutamakan keterbacaan informasi.

---

## 9. Struktur File Frontend (Blade Views)

Untuk menjaga struktur frontend tetap rapi dan mudah dikerjakan bersama tim, folder `resources/views/` diatur berdasarkan area pengguna.

```text
resources/views/
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
│
├── layouts/
│   ├── admin.blade.php
│   ├── peminjam.blade.php
│   └── auth.blade.php
│
├── superadmin/
│   ├── dashboard.blade.php
│   ├── pengadaan/
│   ├── laporan/
│   ├── bengkel/
│   └── toolman/
│
├── toolman/
│   ├── dashboard.blade.php
│   ├── barang/
│   ├── peminjaman/
│   ├── pengembalian/
│   ├── pengadaan/
│   ├── peminjam/
│   └── mutasi/
│
├── peminjam/
│   ├── katalog/
│   ├── pengajuan/
│   └── tiket/
│
└── profile/
```

Struktur folder bersifat high-level dan masih dapat disesuaikan selama proses implementasi tanpa mengubah aturan bisnis utama sistem.

---

## 10. Batasan Scope Prototype

Untuk menjaga prototype tetap fokus, beberapa hal belum menjadi bagian dari scope utama:

- tidak menggunakan pencatatan inventaris berbasis nomor seri individual;
- tidak menggunakan integrasi WhatsApp atau email notification;
- tidak menangani proses pembayaran atau ganti rugi barang;
- approval RAB tidak terhubung otomatis dengan penambahan stok;
- tidak menggunakan relasi many-to-many antara Guru dan Bengkel;
- tidak membuat tabel terpisah untuk Siswa, Guru, maupun Toolman;
- tidak membuat tabel master Jurusan terpisah;
- tidak membuat tabel master Satuan pada tahap prototype;
- `stock_movements` menggunakan model sederhana berbasis `jenis` movement dan belum menggunakan model perpindahan state `dari → ke`.

Fokus prototype adalah memastikan alur utama **master bengkel → pengguna → barang → peminjaman/pengembalian → pengadaan → riwayat stok** dapat berjalan secara konsisten.