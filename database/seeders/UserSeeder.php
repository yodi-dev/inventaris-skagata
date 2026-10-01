<?php

namespace Database\Seeders;

use App\Models\Bengkel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan Bengkel referensi tersedia jika dipanggil mandiri
        $tkj = Bengkel::where('kode', 'TKJ')->first();
        $tkr = Bengkel::where('kode', 'TKR')->first();

        $defaultPassword = Hash::make('password');

        $users = [
            // 1. Waka Sarpras (Super Admin)
            [
                'name'            => 'Waka Sarpras (Demo)',
                'email'           => 'waka@skagata.sch.id',
                'password'        => $defaultPassword,
                'role'            => 'waka',
                'jenis_peminjam'  => null,
                'bengkel_id'      => null,
                'nomor_identitas' => null,
                'nomor_wa'        => '081234567800',
                'status'          => 'aktif',
            ],

            // 2. Kepala Gudang (Super Admin / Waka Role)
            [
                'name'            => 'Kepala Gudang (Demo)',
                'email'           => 'gudang@skagata.sch.id',
                'password'        => $defaultPassword,
                'role'            => 'waka',
                'jenis_peminjam'  => null,
                'bengkel_id'      => null,
                'nomor_identitas' => null,
                'nomor_wa'        => '081234567801',
                'status'          => 'aktif',
            ],

            // 3. Toolman Bengkel TKJ
            [
                'name'            => 'Budi Toolman (TKJ)',
                'email'           => 'toolman.tkj@skagata.sch.id',
                'password'        => $defaultPassword,
                'role'            => 'toolman',
                'jenis_peminjam'  => null,
                'bengkel_id'      => $tkj?->id,
                'nomor_identitas' => null,
                'nomor_wa'        => '081234567802',
                'status'          => 'aktif',
            ],

            // 4. Toolman Bengkel TKR
            [
                'name'            => 'Joko Toolman (TKR)',
                'email'           => 'toolman.tkr@skagata.sch.id',
                'password'        => $defaultPassword,
                'role'            => 'toolman',
                'jenis_peminjam'  => null,
                'bengkel_id'      => $tkr?->id,
                'nomor_identitas' => null,
                'nomor_wa'        => '081234567803',
                'status'          => 'aktif',
            ],

            // 5. Peminjam Siswa TKJ
            [
                'name'            => 'Ahmad Pratama (Siswa TKJ)',
                'email'           => 'siswa.tkj@skagata.sch.id',
                'password'        => $defaultPassword,
                'role'            => 'peminjam',
                'jenis_peminjam'  => 'siswa',
                'bengkel_id'      => $tkj?->id,
                'nomor_identitas' => 'NIS-20240101',
                'nomor_wa'        => '081234567804',
                'status'          => 'aktif',
            ],

            // 6. Peminjam Siswa TKR
            [
                'name'            => 'Rian Ramadhan (Siswa TKR)',
                'email'           => 'siswa.tkr@skagata.sch.id',
                'password'        => $defaultPassword,
                'role'            => 'peminjam',
                'jenis_peminjam'  => 'siswa',
                'bengkel_id'      => $tkr?->id,
                'nomor_identitas' => 'NIS-20240201',
                'nomor_wa'        => '081234567805',
                'status'          => 'aktif',
            ],

            // 7. Peminjam Guru (Lintas Kejuruan)
            [
                'name'            => 'Drs. Hendro Wibowo (Guru)',
                'email'           => 'guru@skagata.sch.id',
                'password'        => $defaultPassword,
                'role'            => 'peminjam',
                'jenis_peminjam'  => 'guru',
                'bengkel_id'      => null,
                'nomor_identitas' => '198501012010011001',
                'nomor_wa'        => '081234567806',
                'status'          => 'aktif',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }
    }
}
