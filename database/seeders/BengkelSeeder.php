<?php

namespace Database\Seeders;

use App\Models\Bengkel;
use Illuminate\Database\Seeder;

class BengkelSeeder extends Seeder
{
    public function run(): void
    {
        $bengkels = [
            [
                'id' => 1,
                'kode' => 'BGK-TKJ',
                'nama' => 'Teknik Komputer & Jaringan (TKJ)',
                'deskripsi' => 'Bengkel kejuruan laboratorium jaringan, server, dan komputer.',
            ],
            [
                'id' => 2,
                'kode' => 'BGK-TKR',
                'nama' => 'Teknik Kendaraan Ringan (TKR)',
                'deskripsi' => 'Bengkel otomotif mobil, engine tune up, dan kelistrikan body.',
            ],
            [
                'id' => 3,
                'kode' => 'BGK-TP',
                'nama' => 'Teknik Pemesinan (TP)',
                'deskripsi' => 'Bengkel bubut, milling, CNC, dan fabrikasi logam.',
            ],
            [
                'id' => 4,
                'kode' => 'BGK-TITL',
                'nama' => 'Teknik Instalasi Tenaga Listrik (TITL)',
                'deskripsi' => 'Bengkel kelistrikan industri, panel daya, dan PLC.',
            ],
        ];

        foreach ($bengkels as $bengkel) {
            Bengkel::updateOrCreate(['id' => $bengkel['id']], $bengkel);
        }
    }
}
