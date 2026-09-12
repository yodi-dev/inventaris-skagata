<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\LokasiPenyimpanan;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $tkj = Bengkel::where('kode', 'BGK-TKJ')->first();
        $tkr = Bengkel::where('kode', 'BGK-TKR')->first();

        // Lokasi TKJ
        $lokasiGuTkj  = LokasiPenyimpanan::where('bengkel_id', $tkj->id)->where('kode', 'LOK-GU')->first();
        $lokasiLaTkj  = LokasiPenyimpanan::where('bengkel_id', $tkj->id)->where('kode', 'LOK-LA')->first();
        $lokasiBhpTkj = LokasiPenyimpanan::where('bengkel_id', $tkj->id)->where('kode', 'LOK-BHP')->first();

        // Lokasi TKR
        $lokasiLpTkr  = LokasiPenyimpanan::where('bengkel_id', $tkr->id)->where('kode', 'LOK-LP')->first();
        $lokasiBhpTkr = LokasiPenyimpanan::where('bengkel_id', $tkr->id)->where('kode', 'LOK-BHP')->first();

        $barangs = [
            // ── INVENTARIS TKJ ────────────────────────────────────────
            // stok_total = stok_tersedia + stok_dipinjam + stok_rusak
            [
                'bengkel_id'            => $tkj->id,
                'lokasi_penyimpanan_id' => $lokasiGuTkj->id,
                'kode_barang'           => 'INV-TKJ-001',
                'nama'                  => 'Router Cisco 891',
                'jenis_barang'          => 'inventaris',
                'satuan'                => 'unit',
                'stok_total'            => 5,
                'stok_tersedia'         => 2,   // 3 sedang dipinjam
                'stok_dipinjam'         => 3,
                'stok_rusak'            => 0,
                'minimum_stok'          => 1,
                'deskripsi'             => 'Router Cisco 891 untuk praktik konfigurasi jaringan WAN.',
            ],
            [
                'bengkel_id'            => $tkj->id,
                'lokasi_penyimpanan_id' => $lokasiGuTkj->id,
                'kode_barang'           => 'INV-TKJ-002',
                'nama'                  => 'Switch Cisco SG110-24',
                'jenis_barang'          => 'inventaris',
                'satuan'                => 'unit',
                'stok_total'            => 3,
                'stok_tersedia'         => 2,   // 1 rusak dari pengembalian sebelumnya
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 1,
                'minimum_stok'          => 1,
                'deskripsi'             => 'Switch manageable 24 port untuk praktik VLAN dan trunking.',
            ],
            [
                'bengkel_id'            => $tkj->id,
                'lokasi_penyimpanan_id' => $lokasiLaTkj->id,
                'kode_barang'           => 'INV-TKJ-003',
                'nama'                  => 'Obeng Phillips Set',
                'jenis_barang'          => 'inventaris',
                'satuan'                => 'set',
                'stok_total'            => 10,
                'stok_tersedia'         => 9,
                'stok_dipinjam'         => 1,
                'stok_rusak'            => 0,
                'minimum_stok'          => 2,
                'deskripsi'             => 'Set obeng Phillips berbagai ukuran untuk bongkar pasang komputer.',
            ],
            [
                'bengkel_id'            => $tkj->id,
                'lokasi_penyimpanan_id' => $lokasiLaTkj->id,
                'kode_barang'           => 'INV-TKJ-004',
                'nama'                  => 'Tang Crimping RJ45',
                'jenis_barang'          => 'inventaris',
                'satuan'                => 'buah',
                'stok_total'            => 5,
                'stok_tersedia'         => 4,
                'stok_dipinjam'         => 1,   // dalam status menunggu pengecekan
                'stok_rusak'            => 0,
                'minimum_stok'          => 1,
                'deskripsi'             => 'Tang crimping untuk pemasangan konektor RJ45 pada kabel UTP.',
            ],

            // ── BHP TKJ ───────────────────────────────────────────────
            [
                'bengkel_id'            => $tkj->id,
                'lokasi_penyimpanan_id' => $lokasiBhpTkj->id,
                'kode_barang'           => 'BHP-TKJ-001',
                'nama'                  => 'Kabel UTP Cat6',
                'jenis_barang'          => 'bhp',
                'satuan'                => 'meter',
                'stok_total'            => 42,  // 50 awal - 8 terpakai
                'stok_tersedia'         => 42,
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 0,
                'minimum_stok'          => 10,
                'deskripsi'             => 'Kabel UTP Cat6 untuk praktik instalasi jaringan LAN.',
            ],
            [
                'bengkel_id'            => $tkj->id,
                'lokasi_penyimpanan_id' => $lokasiBhpTkj->id,
                'kode_barang'           => 'BHP-TKJ-002',
                'nama'                  => 'Konektor RJ45',
                'jenis_barang'          => 'bhp',
                'satuan'                => 'buah',
                'stok_total'            => 185, // 200 awal - 15 terpakai
                'stok_tersedia'         => 185,
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 0,
                'minimum_stok'          => 20,
                'deskripsi'             => 'Konektor RJ45 untuk pengakhiran kabel UTP.',
            ],

            // ── INVENTARIS TKR ────────────────────────────────────────
            [
                'bengkel_id'            => $tkr->id,
                'lokasi_penyimpanan_id' => $lokasiLpTkr->id,
                'kode_barang'           => 'INV-TKR-001',
                'nama'                  => 'Kunci Pas Set 12 pcs',
                'jenis_barang'          => 'inventaris',
                'satuan'                => 'set',
                'stok_total'            => 8,
                'stok_tersedia'         => 6,
                'stok_dipinjam'         => 1,
                'stok_rusak'            => 1,
                'minimum_stok'          => 2,
                'deskripsi'             => 'Set kunci pas kombinasi ukuran 8–19mm untuk tune up mesin.',
            ],
            [
                'bengkel_id'            => $tkr->id,
                'lokasi_penyimpanan_id' => $lokasiLpTkr->id,
                'kode_barang'           => 'INV-TKR-002',
                'nama'                  => 'Kunci Ring Set 14 pcs',
                'jenis_barang'          => 'inventaris',
                'satuan'                => 'set',
                'stok_total'            => 6,
                'stok_tersedia'         => 5,
                'stok_dipinjam'         => 1,   // terlambat
                'stok_rusak'            => 0,
                'minimum_stok'          => 1,
                'deskripsi'             => 'Set kunci ring ganda berbagai ukuran untuk pekerjaan presisi.',
            ],

            // ── BHP TKR ───────────────────────────────────────────────
            [
                'bengkel_id'            => $tkr->id,
                'lokasi_penyimpanan_id' => $lokasiBhpTkr->id,
                'kode_barang'           => 'BHP-TKR-001',
                'nama'                  => 'Oli Mesin SAE 40',
                'jenis_barang'          => 'bhp',
                'satuan'                => 'liter',
                'stok_total'            => 25,  // 30 awal - 5 terpakai
                'stok_tersedia'         => 25,
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 0,
                'minimum_stok'          => 5,
                'deskripsi'             => 'Oli mesin SAE 40 untuk praktik pergantian oli kendaraan.',
            ],
        ];

        foreach ($barangs as $data) {
            Barang::updateOrCreate(
                ['bengkel_id' => $data['bengkel_id'], 'kode_barang' => $data['kode_barang']],
                $data
            );
        }
    }
}
