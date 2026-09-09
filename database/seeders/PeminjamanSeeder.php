<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $tkj = Bengkel::where('kode', 'BGK-TKJ')->first();
        $tkr = Bengkel::where('kode', 'BGK-TKR')->first();

        $toolmanTkj  = User::where('email', 'toolman.tkj@skagata.sch.id')->first();
        $toolmanTkr  = User::where('email', 'toolman.tkr@skagata.sch.id')->first();
        $siswaTkj    = User::where('email', 'siswa.tkj@skagata.sch.id')->first();
        $siswaTkr    = User::where('email', 'siswa.tkr@skagata.sch.id')->first();
        $guru        = User::where('email', 'guru@skagata.sch.id')->first();

        $router  = Barang::where('kode_barang', 'INV-TKJ-001')->first(); // Router Cisco
        $switch  = Barang::where('kode_barang', 'INV-TKJ-002')->first(); // Switch
        $obeng   = Barang::where('kode_barang', 'INV-TKJ-003')->first(); // Obeng
        $crimping = Barang::where('kode_barang', 'INV-TKJ-004')->first(); // Tang Crimping
        $kabel   = Barang::where('kode_barang', 'BHP-TKJ-001')->first(); // Kabel UTP
        $konektor = Barang::where('kode_barang', 'BHP-TKJ-002')->first(); // Konektor RJ45
        $kunciPas = Barang::where('kode_barang', 'INV-TKR-001')->first(); // Kunci Pas
        $kunciRing = Barang::where('kode_barang', 'INV-TKR-002')->first(); // Kunci Ring
        $oli     = Barang::where('kode_barang', 'BHP-TKR-001')->first(); // Oli

        // ── 1. SELESAI — Inventaris (Switch dikembalikan rusak) ───────
        $p1 = Peminjaman::create([
            'user_id'          => $siswaTkj->id,
            'bengkel_id'       => $tkj->id,
            'tanggal_pinjam'   => Carbon::now()->subDays(5)->setTime(8, 0),
            'batas_kembali'    => Carbon::now()->subDays(5)->setTime(16, 0),
            'keperluan'        => 'Praktik konfigurasi jaringan switching VLAN semester 4.',
            'status'           => 'selesai',
            'diproses_oleh'    => $toolmanTkj->id,
            'diproses_pada'    => Carbon::now()->subDays(5)->setTime(8, 15),
            'alasan_penolakan' => null,
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $p1->id,
            'barang_id'     => $switch->id,
            'jumlah'        => 1,
            'jumlah_baik'   => 0,
            'jumlah_rusak'  => 1,
            'jumlah_hilang' => 0,
        ]);

        // ── 2. SELESAI — BHP saja (Kabel UTP diambil) ─────────────────
        $p2 = Peminjaman::create([
            'user_id'          => $siswaTkj->id,
            'bengkel_id'       => $tkj->id,
            'tanggal_pinjam'   => Carbon::now()->subDays(3)->setTime(9, 30),
            'batas_kembali'    => null, // BHP-only
            'keperluan'        => 'Praktik pengkabelan jaringan LAN kelas XI TKJ 2.',
            'status'           => 'selesai',
            'diproses_oleh'    => $toolmanTkj->id,
            'diproses_pada'    => Carbon::now()->subDays(3)->setTime(9, 45),
            'alasan_penolakan' => null,
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $p2->id,
            'barang_id'     => $kabel->id,
            'jumlah'        => 8,
            'jumlah_baik'   => 0,
            'jumlah_rusak'  => 0,
            'jumlah_hilang' => 0,
        ]);

        // ── 3. SELESAI — BHP saja (Konektor RJ45 diambil guru) ────────
        $p3 = Peminjaman::create([
            'user_id'          => $guru->id,
            'bengkel_id'       => $tkj->id,
            'tanggal_pinjam'   => Carbon::now()->subDays(2)->setTime(10, 0),
            'batas_kembali'    => null,
            'keperluan'        => 'Demonstrasi pembuatan kabel straight dan crossover untuk kelas XII.',
            'status'           => 'selesai',
            'diproses_oleh'    => $toolmanTkj->id,
            'diproses_pada'    => Carbon::now()->subDays(2)->setTime(10, 10),
            'alasan_penolakan' => null,
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $p3->id,
            'barang_id'     => $konektor->id,
            'jumlah'        => 15,
            'jumlah_baik'   => 0,
            'jumlah_rusak'  => 0,
            'jumlah_hilang' => 0,
        ]);

        // ── 4. ACTIVE — Inventaris (Guru pinjam Router) ───────────────
        $p4 = Peminjaman::create([
            'user_id'          => $guru->id,
            'bengkel_id'       => $tkj->id,
            'tanggal_pinjam'   => Carbon::now()->setTime(7, 45),
            'batas_kembali'    => Carbon::now()->setTime(15, 30),
            'keperluan'        => 'Praktik konfigurasi routing OSPF kelas XII TKJ 1.',
            'status'           => 'active',
            'diproses_oleh'    => $toolmanTkj->id,
            'diproses_pada'    => Carbon::now()->setTime(7, 50),
            'alasan_penolakan' => null,
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $p4->id,
            'barang_id'     => $router->id,
            'jumlah'        => 3,
            'jumlah_baik'   => 0,
            'jumlah_rusak'  => 0,
            'jumlah_hilang' => 0,
        ]);

        // ── 5. ACTIVE — Inventaris (Siswa TKJ pinjam Obeng) ──────────
        $p5 = Peminjaman::create([
            'user_id'          => $siswaTkj->id,
            'bengkel_id'       => $tkj->id,
            'tanggal_pinjam'   => Carbon::now()->setTime(8, 30),
            'batas_kembali'    => Carbon::now()->setTime(12, 0),
            'keperluan'        => 'Pembongkaran dan perakitan unit PC untuk kegiatan troubleshooting hardware.',
            'status'           => 'active',
            'diproses_oleh'    => $toolmanTkj->id,
            'diproses_pada'    => Carbon::now()->setTime(8, 35),
            'alasan_penolakan' => null,
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $p5->id,
            'barang_id'     => $obeng->id,
            'jumlah'        => 1,
            'jumlah_baik'   => 0,
            'jumlah_rusak'  => 0,
            'jumlah_hilang' => 0,
        ]);

        // ── 6. MENUNGGU PENGECEKAN — Siswa TKJ, Tang Crimping ─────────
        $p6 = Peminjaman::create([
            'user_id'          => $siswaTkj->id,
            'bengkel_id'       => $tkj->id,
            'tanggal_pinjam'   => Carbon::now()->subDay()->setTime(9, 0),
            'batas_kembali'    => Carbon::now()->subDay()->setTime(15, 0),
            'keperluan'        => 'Praktik pemasangan konektor pada kabel UTP untuk ujian kompetensi.',
            'status'           => 'menunggu_pengecekan',
            'diproses_oleh'    => $toolmanTkj->id,
            'diproses_pada'    => Carbon::now()->subDay()->setTime(9, 10),
            'alasan_penolakan' => null,
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $p6->id,
            'barang_id'     => $crimping->id,
            'jumlah'        => 1,
            'jumlah_baik'   => 0, // belum dicek
            'jumlah_rusak'  => 0,
            'jumlah_hilang' => 0,
        ]);

        // ── 7. TERLAMBAT — Siswa TKR, Kunci Ring ─────────────────────
        $p7 = Peminjaman::create([
            'user_id'          => $siswaTkr->id,
            'bengkel_id'       => $tkr->id,
            'tanggal_pinjam'   => Carbon::now()->subDays(2)->setTime(8, 0),
            'batas_kembali'    => Carbon::now()->subDay()->setTime(16, 0), // sudah lewat
            'keperluan'        => 'Praktik tune-up mesin kendaraan ringan semester 5.',
            'status'           => 'terlambat',
            'diproses_oleh'    => $toolmanTkr->id,
            'diproses_pada'    => Carbon::now()->subDays(2)->setTime(8, 20),
            'alasan_penolakan' => null,
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $p7->id,
            'barang_id'     => $kunciRing->id,
            'jumlah'        => 1,
            'jumlah_baik'   => 0,
            'jumlah_rusak'  => 0,
            'jumlah_hilang' => 0,
        ]);

        // ── 8. ACTIVE — Siswa TKR, Kunci Pas ─────────────────────────
        $p8 = Peminjaman::create([
            'user_id'          => $siswaTkr->id,
            'bengkel_id'       => $tkr->id,
            'tanggal_pinjam'   => Carbon::now()->setTime(7, 30),
            'batas_kembali'    => Carbon::now()->setTime(16, 0),
            'keperluan'        => 'Praktik overhaul engine dan pengecekan celah klep.',
            'status'           => 'active',
            'diproses_oleh'    => $toolmanTkr->id,
            'diproses_pada'    => Carbon::now()->setTime(7, 40),
            'alasan_penolakan' => null,
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $p8->id,
            'barang_id'     => $kunciPas->id,
            'jumlah'        => 1,
            'jumlah_baik'   => 0,
            'jumlah_rusak'  => 0,
            'jumlah_hilang' => 0,
        ]);

        // ── 9. SELESAI — BHP TKR (Oli diambil siswa TKR) ─────────────
        $p9 = Peminjaman::create([
            'user_id'          => $siswaTkr->id,
            'bengkel_id'       => $tkr->id,
            'tanggal_pinjam'   => Carbon::now()->subDays(4)->setTime(9, 0),
            'batas_kembali'    => null,
            'keperluan'        => 'Praktik pergantian oli mesin mobil Toyota Avanza.',
            'status'           => 'selesai',
            'diproses_oleh'    => $toolmanTkr->id,
            'diproses_pada'    => Carbon::now()->subDays(4)->setTime(9, 15),
            'alasan_penolakan' => null,
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $p9->id,
            'barang_id'     => $oli->id,
            'jumlah'        => 5,
            'jumlah_baik'   => 0,
            'jumlah_rusak'  => 0,
            'jumlah_hilang' => 0,
        ]);

        // ── 10. DITOLAK — Guru, Router (stok tidak cukup) ─────────────
        $p10 = Peminjaman::create([
            'user_id'          => $guru->id,
            'bengkel_id'       => $tkj->id,
            'tanggal_pinjam'   => Carbon::now()->subDay()->setTime(7, 0),
            'batas_kembali'    => Carbon::now()->subDay()->setTime(15, 0),
            'keperluan'        => 'Praktik konfigurasi jaringan WAN untuk seluruh kelas XII.',
            'status'           => 'ditolak',
            'diproses_oleh'    => $toolmanTkj->id,
            'diproses_pada'    => Carbon::now()->subDay()->setTime(7, 20),
            'alasan_penolakan' => 'Stok Router Cisco 891 tidak mencukupi. Saat ini hanya tersedia 2 unit.',
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $p10->id,
            'barang_id'     => $router->id,
            'jumlah'        => 5,
            'jumlah_baik'   => 0,
            'jumlah_rusak'  => 0,
            'jumlah_hilang' => 0,
        ]);

        // ── 11. PENDING — Siswa TKJ, kombinasi Inventaris + BHP ───────
        Peminjaman::create([
            'user_id'          => $siswaTkj->id,
            'bengkel_id'       => $tkj->id,
            'tanggal_pinjam'   => Carbon::now()->subHours(2),
            'batas_kembali'    => Carbon::now()->setTime(15, 0),
            'keperluan'        => 'Praktik instalasi jaringan dari awal, butuh alat dan kabel.',
            'status'           => 'pending',
            'diproses_oleh'    => null,
            'diproses_pada'    => null,
            'alasan_penolakan' => null,
        ])->detailPeminjamans()->createMany([
            ['barang_id' => $crimping->id, 'jumlah' => 1, 'jumlah_baik' => 0, 'jumlah_rusak' => 0, 'jumlah_hilang' => 0],
            ['barang_id' => $kabel->id,    'jumlah' => 5, 'jumlah_baik' => 0, 'jumlah_rusak' => 0, 'jumlah_hilang' => 0],
            ['barang_id' => $konektor->id, 'jumlah' => 10, 'jumlah_baik' => 0, 'jumlah_rusak' => 0, 'jumlah_hilang' => 0],
        ]);
    }
}
