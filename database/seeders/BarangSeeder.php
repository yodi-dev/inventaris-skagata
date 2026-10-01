<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\LokasiPenyimpanan;
use App\Models\SumberDana;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $tkj = Bengkel::where('kode', 'TKJ')->first();
        $tkr = Bengkel::where('kode', 'TKR')->first();

        if (!$tkj || !$tkr) {
            return;
        }

        $bos  = SumberDana::where('nama', 'BOS Reguler')->first();
        $bopd = SumberDana::where('nama', 'BOPD / Komite')->first();
        $dak  = SumberDana::where('nama', 'DAK Fisik')->first();

        // 1. Barang Sampel Bengkel TKJ
        $lokasiRackTKJ = LokasiPenyimpanan::where('bengkel_id', $tkj->id)->where('kode', 'L-TKJ-01')->first();
        $lokasiRakTKJ  = LokasiPenyimpanan::where('bengkel_id', $tkj->id)->where('kode', 'R-TKJ-01')->first();

        $itemsTKJ = [
            [
                'bengkel_id'            => $tkj->id,
                'lokasi_penyimpanan_id' => $lokasiRackTKJ?->id,
                'sumber_dana_id'        => $bos?->id,
                'kode_barang'           => 'TKJ-INV-001',
                'nama'                  => 'Routerboard MikroTik RB750Gr3',
                'jenis_barang'          => 'inventaris',
                'satuan'                => 'Unit',
                'harga'                 => 850000,
                'stok_total'            => 10,
                'stok_tersedia'         => 10,
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 0,
                'minimum_stok'          => 2,
                'deskripsi'             => 'Routerboard 5-port Gigabit Ethernet untuk praktikum konfigurasi routing lab TKJ.',
            ],
            [
                'bengkel_id'            => $tkj->id,
                'lokasi_penyimpanan_id' => $lokasiRakTKJ?->id,
                'sumber_dana_id'        => $bopd?->id,
                'kode_barang'           => 'TKJ-INV-002',
                'nama'                  => "Crimping Tool RJ45 Pro'sKit",
                'jenis_barang'          => 'inventaris',
                'satuan'                => 'Pcs',
                'harga'                 => 175000,
                'stok_total'            => 15,
                'stok_tersedia'         => 15,
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 0,
                'minimum_stok'          => 3,
                'deskripsi'             => 'Tang crimping kabel jaringan presisi tinggi dengan strip & cutter terpadu.',
            ],
            [
                'bengkel_id'            => $tkj->id,
                'lokasi_penyimpanan_id' => $lokasiRakTKJ?->id,
                'sumber_dana_id'        => $bos?->id,
                'kode_barang'           => 'TKJ-BHP-001',
                'nama'                  => 'Konektor RJ45 Cat6 Belden',
                'jenis_barang'          => 'bhp',
                'satuan'                => 'Box',
                'harga'                 => 120000,
                'stok_total'            => 50,
                'stok_tersedia'         => 50,
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 0,
                'minimum_stok'          => 10,
                'deskripsi'             => 'Konektor modular RJ45 Cat6 kemasan box (50 pcs) untuk praktikum kabel LAN.',
            ],
            [
                'bengkel_id'            => $tkj->id,
                'lokasi_penyimpanan_id' => $lokasiRakTKJ?->id,
                'sumber_dana_id'        => $dak?->id,
                'kode_barang'           => 'TKJ-BHP-002',
                'nama'                  => 'Kabel UTP Cat6 305m Belden',
                'jenis_barang'          => 'bhp',
                'satuan'                => 'Roll',
                'harga'                 => 1450000,
                'stok_total'            => 5,
                'stok_tersedia'         => 5,
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 0,
                'minimum_stok'          => 1,
                'deskripsi'             => 'Roll kabel UTP Cat6 standar 305 meter untuk instalasi jaringan lokal.',
            ],
        ];

        foreach ($itemsTKJ as $item) {
            Barang::updateOrCreate(
                [
                    'bengkel_id'  => $item['bengkel_id'],
                    'kode_barang' => $item['kode_barang'],
                ],
                $item
            );
        }

        // 2. Barang Sampel Bengkel TKR
        $lokasiLemariTKR = LokasiPenyimpanan::where('bengkel_id', $tkr->id)->where('kode', 'L-TKR-01')->first();
        $lokasiRakTKR    = LokasiPenyimpanan::where('bengkel_id', $tkr->id)->where('kode', 'R-TKR-01')->first();

        $itemsTKR = [
            [
                'bengkel_id'            => $tkr->id,
                'lokasi_penyimpanan_id' => $lokasiLemariTKR?->id,
                'sumber_dana_id'        => $dak?->id,
                'kode_barang'           => 'TKR-INV-001',
                'nama'                  => 'OBD2 Diagnostic Scanner Tool',
                'jenis_barang'          => 'inventaris',
                'satuan'                => 'Unit',
                'harga'                 => 3500000,
                'stok_total'            => 4,
                'stok_tersedia'         => 4,
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 0,
                'minimum_stok'          => 1,
                'deskripsi'             => 'Alat pemindai diagnostik sistem injeksi EFI dan sensor kelistrikan mobil.',
            ],
            [
                'bengkel_id'            => $tkr->id,
                'lokasi_penyimpanan_id' => $lokasiRakTKR?->id,
                'sumber_dana_id'        => $bos?->id,
                'kode_barang'           => 'TKR-INV-002',
                'nama'                  => 'Kunci Momen / Torque Wrench 1/2"',
                'jenis_barang'          => 'inventaris',
                'satuan'                => 'Set',
                'harga'                 => 650000,
                'stok_total'            => 8,
                'stok_tersedia'         => 8,
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 0,
                'minimum_stok'          => 2,
                'deskripsi'             => 'Kunci torsi pengencangan baut kepala silinder bersertifikasi presisi.',
            ],
            [
                'bengkel_id'            => $tkr->id,
                'lokasi_penyimpanan_id' => $lokasiRakTKR?->id,
                'sumber_dana_id'        => $bopd?->id,
                'kode_barang'           => 'TKR-BHP-001',
                'nama'                  => 'Brake Cleaner Aerosol 500ml',
                'jenis_barang'          => 'bhp',
                'satuan'                => 'Pcs',
                'harga'                 => 45000,
                'stok_total'            => 30,
                'stok_tersedia'         => 30,
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 0,
                'minimum_stok'          => 5,
                'deskripsi'             => 'Cairan semprot pembersih debu rem dan gemuk kampas rem mobil.',
            ],
            [
                'bengkel_id'            => $tkr->id,
                'lokasi_penyimpanan_id' => $lokasiRakTKR?->id,
                'sumber_dana_id'        => $bopd?->id,
                'kode_barang'           => 'TKR-BHP-002',
                'nama'                  => 'Kertas Gosok Amplas Grid 400',
                'jenis_barang'          => 'bhp',
                'satuan'                => 'Pcs',
                'harga'                 => 5000,
                'stok_total'            => 100,
                'stok_tersedia'         => 100,
                'stok_dipinjam'         => 0,
                'stok_rusak'            => 0,
                'minimum_stok'          => 20,
                'deskripsi'             => 'Amplas halus grid 400 untuk pembersihan kerak katup dan permukaan logam.',
            ],
        ];

        foreach ($itemsTKR as $item) {
            Barang::updateOrCreate(
                [
                    'bengkel_id'  => $item['bengkel_id'],
                    'kode_barang' => $item['kode_barang'],
                ],
                $item
            );
        }
    }
}
