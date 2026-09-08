<?php

namespace Database\Seeders;

use App\Models\Bengkel;
use App\Models\LokasiPenyimpanan;
use Illuminate\Database\Seeder;

class LokasiPenyimpananSeeder extends Seeder
{
    public function run(): void
    {
        $tkj  = Bengkel::where('kode', 'BGK-TKJ')->first();
        $tkr  = Bengkel::where('kode', 'BGK-TKR')->first();
        $tp   = Bengkel::where('kode', 'BGK-TP')->first();
        $titl = Bengkel::where('kode', 'BGK-TITL')->first();

        $lokasis = [
            // ── TKJ ──────────────────────────────────────────────────
            [
                'bengkel_id' => $tkj->id,
                'kode'       => 'LOK-GU',
                'nama'       => 'Gudang Utama',
                'deskripsi'  => 'Gudang penyimpanan utama perangkat keras dan peralatan besar seperti router dan switch.',
            ],
            [
                'bengkel_id' => $tkj->id,
                'kode'       => 'LOK-LA',
                'nama'       => 'Lemari Alat A',
                'deskripsi'  => 'Lemari penyimpanan alat tangan dan set peralatan instalasi jaringan.',
            ],
            [
                'bengkel_id' => $tkj->id,
                'kode'       => 'LOK-BHP',
                'nama'       => 'Rak BHP',
                'deskripsi'  => 'Rak khusus bahan habis pakai seperti kabel UTP dan konektor RJ45.',
            ],

            // ── TKR ──────────────────────────────────────────────────
            [
                'bengkel_id' => $tkr->id,
                'kode'       => 'LOK-GO',
                'nama'       => 'Gudang Otomotif',
                'deskripsi'  => 'Gudang penyimpanan alat mekanik, dongkrak, dan peralatan otomotif besar.',
            ],
            [
                'bengkel_id' => $tkr->id,
                'kode'       => 'LOK-LP',
                'nama'       => 'Lemari Peralatan',
                'deskripsi'  => 'Lemari penyimpanan alat tangan mekanik seperti kunci pas dan kunci ring.',
            ],
            [
                'bengkel_id' => $tkr->id,
                'kode'       => 'LOK-BHP',
                'nama'       => 'Rak BHP Otomotif',
                'deskripsi'  => 'Rak bahan habis pakai seperti oli, minyak rem, dan amplas.',
            ],

            // ── TP ───────────────────────────────────────────────────
            [
                'bengkel_id' => $tp->id,
                'kode'       => 'LOK-GM',
                'nama'       => 'Gudang Mesin',
                'deskripsi'  => 'Gudang penyimpanan pahat, bor, dan alat potong mesin.',
            ],
            [
                'bengkel_id' => $tp->id,
                'kode'       => 'LOK-BHP',
                'nama'       => 'Rak BHP Pemesinan',
                'deskripsi'  => 'Rak bahan habis pakai seperti mata bor, amplas, dan coolant.',
            ],

            // ── TITL ─────────────────────────────────────────────────
            [
                'bengkel_id' => $titl->id,
                'kode'       => 'LOK-LK',
                'nama'       => 'Lemari Kelistrikan',
                'deskripsi'  => 'Lemari penyimpanan alat ukur listrik dan komponen panel.',
            ],
            [
                'bengkel_id' => $titl->id,
                'kode'       => 'LOK-BHP',
                'nama'       => 'Rak BHP Listrik',
                'deskripsi'  => 'Rak bahan habis pakai seperti kabel NYA, isolasi, dan sekering.',
            ],
        ];

        foreach ($lokasis as $data) {
            LokasiPenyimpanan::updateOrCreate(
                ['bengkel_id' => $data['bengkel_id'], 'kode' => $data['kode']],
                $data
            );
        }
    }
}
