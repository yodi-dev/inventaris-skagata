<?php

namespace Database\Seeders;

use App\Models\Bengkel;
use App\Models\LokasiPenyimpanan;
use App\Models\Satuan;
use App\Models\SumberDana;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bengkel Kejuruan
        $bengkels = [
            [
                'kode'      => 'TKJ',
                'nama'      => 'Teknik Komputer dan Jaringan',
                'deskripsi' => 'Konsentrasi keahlian Teknik Komputer dan Jaringan SMK N 1 Ngawen',
            ],
            [
                'kode'      => 'TKR',
                'nama'      => 'Teknik Kendaraan Ringan Otomotif',
                'deskripsi' => 'Konsentrasi keahlian Teknik Kendaraan Ringan Otomotif SMK N 1 Ngawen',
            ],
        ];

        $bengkelModels = [];
        foreach ($bengkels as $bengkelData) {
            $bengkelModels[$bengkelData['kode']] = Bengkel::updateOrCreate(
                ['kode' => $bengkelData['kode']],
                $bengkelData
            );
        }

        // 2. Master Satuan
        $satuans = [
            ['nama' => 'Unit', 'singkatan' => 'unit', 'deskripsi' => 'Satuan per unit perangkat / mesin'],
            ['nama' => 'Pcs',  'singkatan' => 'pcs',  'deskripsi' => 'Satuan per buah / biji / keping'],
            ['nama' => 'Set',  'singkatan' => 'set',  'deskripsi' => 'Satuan per perangkat lengkap / kit'],
            ['nama' => 'Roll', 'singkatan' => 'roll', 'deskripsi' => 'Satuan per gulung kabel / kawat'],
            ['nama' => 'Box',  'singkatan' => 'box',  'deskripsi' => 'Satuan per kotak / dus kemasan'],
        ];

        foreach ($satuans as $satuanData) {
            Satuan::updateOrCreate(
                ['nama' => $satuanData['nama']],
                $satuanData
            );
        }

        // 3. Master Sumber Dana
        $sumberDanas = [
            ['kode' => 'BOS',  'nama' => 'BOS Reguler',     'deskripsi' => 'Bantuan Operasional Sekolah Reguler'],
            ['kode' => 'BOPD', 'nama' => 'BOPD / Komite',   'deskripsi' => 'Bantuan Operasional Pendidikan Daerah & Komite'],
            ['kode' => 'DAK',  'nama' => 'DAK Fisik',       'deskripsi' => 'Dana Alokasi Khusus Fisik Sarpras Pendidikan'],
        ];

        foreach ($sumberDanas as $sumberData) {
            SumberDana::updateOrCreate(
                ['nama' => $sumberData['nama']],
                $sumberData
            );
        }

        // 4. Master Lokasi Penyimpanan per Bengkel
        if (isset($bengkelModels['TKJ'])) {
            $lokasiTKJ = [
                [
                    'bengkel_id' => $bengkelModels['TKJ']->id,
                    'kode'       => 'L-TKJ-01',
                    'nama'       => 'Lemari Rack Jaringan & Server',
                    'deskripsi'  => 'Penyimpanan router, switch, access point, dan server lab TKJ',
                ],
                [
                    'bengkel_id' => $bengkelModels['TKJ']->id,
                    'kode'       => 'R-TKJ-01',
                    'nama'       => 'Rak Toolset & Kabel Jaringan',
                    'deskripsi'  => 'Penyimpanan tang crimping, tester LAN, kabel roll, dan konektor',
                ],
            ];

            foreach ($lokasiTKJ as $lokasi) {
                LokasiPenyimpanan::updateOrCreate(
                    [
                        'bengkel_id' => $lokasi['bengkel_id'],
                        'kode'       => $lokasi['kode'],
                    ],
                    $lokasi
                );
            }
        }

        if (isset($bengkelModels['TKR'])) {
            $lokasiTKR = [
                [
                    'bengkel_id' => $bengkelModels['TKR']->id,
                    'kode'       => 'L-TKR-01',
                    'nama'       => 'Lemari SST & Diagnostik Scanner',
                    'deskripsi'  => 'Penyimpanan alat SST dan scan tool diagnostic OBD2 bengkel TKR',
                ],
                [
                    'bengkel_id' => $bengkelModels['TKR']->id,
                    'kode'       => 'R-TKR-01',
                    'nama'       => 'Rak Perkakas Tangan Mekanik',
                    'deskripsi'  => 'Penyimpanan kunci momen, obeng, tang, dan perkakas tangan bengkel',
                ],
            ];

            foreach ($lokasiTKR as $lokasi) {
                LokasiPenyimpanan::updateOrCreate(
                    [
                        'bengkel_id' => $lokasi['bengkel_id'],
                        'kode'       => $lokasi['kode'],
                    ],
                    $lokasi
                );
            }
        }
    }
}
