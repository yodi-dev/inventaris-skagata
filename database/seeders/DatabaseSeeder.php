<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            BengkelSeeder::class,
            UserSeeder::class,
            BengkelSeeder::class,           // 1. Master bengkel
            LokasiPenyimpananSeeder::class, // 2. Lokasi penyimpanan (butuh bengkel)
            UserSeeder::class,              // 3. User / pengguna (butuh bengkel)
            BarangSeeder::class,            // 4. Barang (butuh bengkel + lokasi)
            PeminjamanSeeder::class,        // 5. Peminjaman + detail (butuh user + barang)
            PengadaanSeeder::class,         // 6. Pengadaan + detail (butuh user + barang)
            StockMovementSeeder::class,     // 7. Riwayat stok (butuh barang + peminjaman)
        ]);
    }
}
