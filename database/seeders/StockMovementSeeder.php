<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $toolmanTkj = User::where('email', 'toolman.tkj@skagata.sch.id')->first();
        $toolmanTkr = User::where('email', 'toolman.tkr@skagata.sch.id')->first();

        $router   = Barang::where('kode_barang', 'INV-TKJ-001')->first();
        $switch   = Barang::where('kode_barang', 'INV-TKJ-002')->first();
        $obeng    = Barang::where('kode_barang', 'INV-TKJ-003')->first();
        $crimping = Barang::where('kode_barang', 'INV-TKJ-004')->first();
        $kabel    = Barang::where('kode_barang', 'BHP-TKJ-001')->first();
        $konektor = Barang::where('kode_barang', 'BHP-TKJ-002')->first();
        $kunciPas = Barang::where('kode_barang', 'INV-TKR-001')->first();
        $kunciRing = Barang::where('kode_barang', 'INV-TKR-002')->first();
        $oli      = Barang::where('kode_barang', 'BHP-TKR-001')->first();

        // Ambil ID peminjaman berdasarkan urutan seeder (keperluan unik)
        $pSwitch       = Peminjaman::where('keperluan', 'LIKE', '%switching VLAN%')->first();
        $pKabelBhp     = Peminjaman::where('keperluan', 'LIKE', '%pengkabelan jaringan LAN%')->first();
        $pKonektorGuru = Peminjaman::where('keperluan', 'LIKE', '%kabel straight dan crossover%')->first();
        $pRouterGuru   = Peminjaman::where('keperluan', 'LIKE', '%routing OSPF%')->first();
        $pObengSiswa   = Peminjaman::where('keperluan', 'LIKE', '%troubleshooting hardware%')->first();
        $pCrimping     = Peminjaman::where('keperluan', 'LIKE', '%ujian kompetensi%')->first();
        $pKunciRingTkr = Peminjaman::where('keperluan', 'LIKE', '%tune-up mesin kendaraan%')->first();
        $pKunciPasTkr  = Peminjaman::where('keperluan', 'LIKE', '%overhaul engine%')->first();
        $pOliBhp       = Peminjaman::where('keperluan', 'LIKE', '%pergantian oli%')->first();

        $movements = [

            // ════════════════════════════════════════════════════════
            // STOK MASUK AWAL — TKJ (seolah dilakukan saat awal setup)
            // ════════════════════════════════════════════════════════
            [
                'barang_id'      => $router->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'stok_masuk',
                'jumlah'         => 5,
                'referensi_tipe' => null,
                'referensi_id'   => null,
                'keterangan'     => 'Stok awal Router Cisco 891 diterima dari gudang sekolah.',
                'created_at'     => Carbon::now()->subMonths(3),
            ],
            [
                'barang_id'      => $switch->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'stok_masuk',
                'jumlah'         => 3,
                'referensi_tipe' => null,
                'referensi_id'   => null,
                'keterangan'     => 'Stok awal Switch Cisco SG110-24 diterima dari hasil RAB tahun lalu.',
                'created_at'     => Carbon::now()->subMonths(3),
            ],
            [
                'barang_id'      => $obeng->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'stok_masuk',
                'jumlah'         => 10,
                'referensi_tipe' => null,
                'referensi_id'   => null,
                'keterangan'     => 'Stok awal Obeng Phillips Set diterima.',
                'created_at'     => Carbon::now()->subMonths(3),
            ],
            [
                'barang_id'      => $crimping->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'stok_masuk',
                'jumlah'         => 5,
                'referensi_tipe' => null,
                'referensi_id'   => null,
                'keterangan'     => 'Stok awal Tang Crimping RJ45 diterima.',
                'created_at'     => Carbon::now()->subMonths(3),
            ],
            [
                'barang_id'      => $kabel->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'stok_masuk',
                'jumlah'         => 50,
                'referensi_tipe' => null,
                'referensi_id'   => null,
                'keterangan'     => 'Stok awal Kabel UTP Cat6 50 meter diterima.',
                'created_at'     => Carbon::now()->subMonths(3),
            ],
            [
                'barang_id'      => $konektor->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'stok_masuk',
                'jumlah'         => 200,
                'referensi_tipe' => null,
                'referensi_id'   => null,
                'keterangan'     => 'Stok awal Konektor RJ45 200 buah diterima.',
                'created_at'     => Carbon::now()->subMonths(3),
            ],

            // ════════════════════════════════════════════════════════
            // STOK MASUK AWAL — TKR
            // ════════════════════════════════════════════════════════
            [
                'barang_id'      => $kunciPas->id,
                'user_id'        => $toolmanTkr->id,
                'jenis'          => 'stok_masuk',
                'jumlah'         => 8,
                'referensi_tipe' => null,
                'referensi_id'   => null,
                'keterangan'     => 'Stok awal Kunci Pas Set 12 pcs diterima.',
                'created_at'     => Carbon::now()->subMonths(3),
            ],
            [
                'barang_id'      => $kunciRing->id,
                'user_id'        => $toolmanTkr->id,
                'jenis'          => 'stok_masuk',
                'jumlah'         => 6,
                'referensi_tipe' => null,
                'referensi_id'   => null,
                'keterangan'     => 'Stok awal Kunci Ring Set 14 pcs diterima.',
                'created_at'     => Carbon::now()->subMonths(3),
            ],
            [
                'barang_id'      => $oli->id,
                'user_id'        => $toolmanTkr->id,
                'jenis'          => 'stok_masuk',
                'jumlah'         => 30,
                'referensi_tipe' => null,
                'referensi_id'   => null,
                'keterangan'     => 'Stok awal Oli Mesin SAE 40 30 liter diterima.',
                'created_at'     => Carbon::now()->subMonths(3),
            ],

            // ════════════════════════════════════════════════════════
            // PENYESUAIAN — Kunci Pas TKR (1 rusak tercatat sebelumnya)
            // ════════════════════════════════════════════════════════
            [
                'barang_id'      => $kunciPas->id,
                'user_id'        => $toolmanTkr->id,
                'jenis'          => 'penyesuaian',
                'jumlah'         => 1,
                'referensi_tipe' => null,
                'referensi_id'   => null,
                'keterangan'     => 'Penyesuaian stok: 1 unit Kunci Pas Set ditemukan dalam kondisi rusak berat saat inventarisasi.',
                'created_at'     => Carbon::now()->subMonths(1),
            ],

            // ════════════════════════════════════════════════════════
            // PEMINJAMAN — Riwayat yang sudah selesai
            // ════════════════════════════════════════════════════════

            // P1: Siswa TKJ pinjam Switch → kembali rusak
            [
                'barang_id'      => $switch->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'peminjaman',
                'jumlah'         => 1,
                'referensi_tipe' => 'peminjaman',
                'referensi_id'   => $pSwitch?->id,
                'keterangan'     => 'Switch dipinjam siswa untuk praktik VLAN.',
                'created_at'     => Carbon::now()->subDays(5)->setTime(8, 15),
            ],
            [
                'barang_id'      => $switch->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'pengembalian_rusak',
                'jumlah'         => 1,
                'referensi_tipe' => 'peminjaman',
                'referensi_id'   => $pSwitch?->id,
                'keterangan'     => 'Switch dikembalikan dalam kondisi rusak (port 1-4 tidak berfungsi).',
                'created_at'     => Carbon::now()->subDays(5)->setTime(16, 20),
            ],

            // P2: Siswa TKJ ambil Kabel UTP (BHP)
            [
                'barang_id'      => $kabel->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'bhp_keluar',
                'jumlah'         => 8,
                'referensi_tipe' => 'peminjaman',
                'referensi_id'   => $pKabelBhp?->id,
                'keterangan'     => 'Kabel UTP 8 meter diambil siswa untuk praktik pengkabelan LAN.',
                'created_at'     => Carbon::now()->subDays(3)->setTime(9, 45),
            ],

            // P3: Guru ambil Konektor RJ45 (BHP)
            [
                'barang_id'      => $konektor->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'bhp_keluar',
                'jumlah'         => 15,
                'referensi_tipe' => 'peminjaman',
                'referensi_id'   => $pKonektorGuru?->id,
                'keterangan'     => 'Konektor RJ45 15 buah diambil guru untuk demonstrasi kelas.',
                'created_at'     => Carbon::now()->subDays(2)->setTime(10, 10),
            ],

            // P4: Guru pinjam Router 3 unit (masih active)
            [
                'barang_id'      => $router->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'peminjaman',
                'jumlah'         => 3,
                'referensi_tipe' => 'peminjaman',
                'referensi_id'   => $pRouterGuru?->id,
                'keterangan'     => 'Router dipinjam guru untuk praktik routing OSPF.',
                'created_at'     => Carbon::now()->setTime(7, 50),
            ],

            // P5: Siswa TKJ pinjam Obeng (masih active)
            [
                'barang_id'      => $obeng->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'peminjaman',
                'jumlah'         => 1,
                'referensi_tipe' => 'peminjaman',
                'referensi_id'   => $pObengSiswa?->id,
                'keterangan'     => 'Obeng dipinjam siswa untuk troubleshooting hardware.',
                'created_at'     => Carbon::now()->setTime(8, 35),
            ],

            // P6: Siswa TKJ pinjam Tang Crimping (menunggu pengecekan)
            [
                'barang_id'      => $crimping->id,
                'user_id'        => $toolmanTkj->id,
                'jenis'          => 'peminjaman',
                'jumlah'         => 1,
                'referensi_tipe' => 'peminjaman',
                'referensi_id'   => $pCrimping?->id,
                'keterangan'     => 'Tang Crimping dipinjam siswa untuk ujian kompetensi.',
                'created_at'     => Carbon::now()->subDay()->setTime(9, 10),
            ],

            // P7: Siswa TKR pinjam Kunci Ring (terlambat)
            [
                'barang_id'      => $kunciRing->id,
                'user_id'        => $toolmanTkr->id,
                'jenis'          => 'peminjaman',
                'jumlah'         => 1,
                'referensi_tipe' => 'peminjaman',
                'referensi_id'   => $pKunciRingTkr?->id,
                'keterangan'     => 'Kunci Ring dipinjam siswa TKR, belum dikembalikan (terlambat).',
                'created_at'     => Carbon::now()->subDays(2)->setTime(8, 20),
            ],

            // P8: Siswa TKR pinjam Kunci Pas (active)
            [
                'barang_id'      => $kunciPas->id,
                'user_id'        => $toolmanTkr->id,
                'jenis'          => 'peminjaman',
                'jumlah'         => 1,
                'referensi_tipe' => 'peminjaman',
                'referensi_id'   => $pKunciPasTkr?->id,
                'keterangan'     => 'Kunci Pas dipinjam siswa TKR untuk overhaul engine.',
                'created_at'     => Carbon::now()->setTime(7, 40),
            ],

            // P9: Siswa TKR ambil Oli (BHP selesai)
            [
                'barang_id'      => $oli->id,
                'user_id'        => $toolmanTkr->id,
                'jenis'          => 'bhp_keluar',
                'jumlah'         => 5,
                'referensi_tipe' => 'peminjaman',
                'referensi_id'   => $pOliBhp?->id,
                'keterangan'     => 'Oli SAE 40 5 liter diambil siswa TKR untuk praktik ganti oli.',
                'created_at'     => Carbon::now()->subDays(4)->setTime(9, 15),
            ],
        ];

        foreach ($movements as $data) {
            StockMovement::create($data);
        }
    }
}
