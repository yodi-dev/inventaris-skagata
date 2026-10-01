<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // 1. Waka Sarpras (Super Admin)
            [
                'name'            => 'Waka Sarpras',
                'email'           => 'waka@skagata.sch.id',
                'password'        => Hash::make('password123'),
                'role'            => 'waka',
                'jenis_peminjam'  => null,
                'bengkel_id'      => null,
                'nomor_identitas' => null, // Dikosongkan sesuai kebijakan awal
                'nomor_wa'        => null,
                'status'          => 'aktif',
            ],

            // 2. Kepala Gudang (Super Admin / Waka Role)
            [
                'name'            => 'Kepala Gudang',
                'email'           => 'gudang@skagata.sch.id',
                'password'        => Hash::make('password123'),
                'role'            => 'waka',
                'jenis_peminjam'  => null,
                'bengkel_id'      => null,
                'nomor_identitas' => null, // Dikosongkan sesuai kebijakan awal
                'nomor_wa'        => null,
                'status'          => 'aktif',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }
    }
}
