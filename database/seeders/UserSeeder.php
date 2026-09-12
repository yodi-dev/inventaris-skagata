<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Bengkel;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tkj  = Bengkel::where('kode', 'BGK-TKJ')->first();
        $tkr  = Bengkel::where('kode', 'BGK-TKR')->first();

        $users = [
            // 1. Waka Sarpras (Super Admin)
            // ── Waka Sarpras (Super Admin) ────────────────────────────
            [
                'email' => 'waka@skagata.sch.id',
                'name' => 'Drs. H. Ahmad Riyadi, M.Pd. (Waka Sarpras)',
                'password' => Hash::make('password123'),
                'role' => 'waka',
                'jenis_peminjam' => null,
                'bengkel_id' => null,
                'email'           => 'waka@skagata.sch.id',
                'name'            => 'Drs. H. Ahmad Riyadi, M.Pd.',
                'password'        => Hash::make('password123'),
                'role'            => 'waka',
                'jenis_peminjam'  => null,
                'bengkel_id'      => null,
                'nomor_identitas' => '19750814 200003 1 002',
                'nomor_wa' => '081234567890',
                'status' => 'aktif',
                'nomor_wa'        => '081234567890',
                'status'          => 'aktif',
            ],

            // 2. Admin Bengkel (Toolman)
            // ── Toolman TKJ ───────────────────────────────────────────
            [
                'email' => 'toolman@skagata.sch.id',
                'name' => 'Bambang Wijaya, S.T. (Toolman TKJ)',
                'password' => Hash::make('password123'),
                'role' => 'toolman',
                'jenis_peminjam' => null,
                'bengkel_id' => 1, // TKJ
                'email'           => 'toolman.tkj@skagata.sch.id',
                'name'            => 'Bambang Wijaya, S.T.',
                'password'        => Hash::make('password123'),
                'role'            => 'toolman',
                'jenis_peminjam'  => null,
                'bengkel_id'      => $tkj->id,
                'nomor_identitas' => '19880315 201201 1 004',
                'nomor_wa' => '081398765432',
                'status' => 'aktif',
                'nomor_wa'        => '081398765432',
                'status'          => 'aktif',
            ],

            // 3. Peminjam Siswa Aktif
            // ── Toolman TKR ───────────────────────────────────────────
            [
                'email' => 'siswa@skagata.sch.id',
                'name' => 'Budi Santoso (Siswa Aktif)',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'jenis_peminjam' => 'siswa',
                'bengkel_id' => 1, // TKJ
                'nomor_identitas' => '12345',
                'nomor_wa' => '085711223344',
                'status' => 'aktif',
                'email'           => 'toolman.tkr@skagata.sch.id',
                'name'            => 'Agus Setiawan, S.Pd.T.',
                'password'        => Hash::make('password123'),
                'role'            => 'toolman',
                'jenis_peminjam'  => null,
                'bengkel_id'      => $tkr->id,
                'nomor_identitas' => '19850720 201001 1 003',
                'nomor_wa'        => '082112345678',
                'status'          => 'aktif',
            ],

            // 4. Peminjam Guru Aktif
            // ── Siswa TKJ – Aktif ─────────────────────────────────────
            [
                'email' => 'guru@skagata.sch.id',
                'name' => 'Pak Yono, S.Pd.T. (Guru)',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'jenis_peminjam' => 'guru',
                'bengkel_id' => null, // Guru tidak terikat bengkel
                'email'           => 'siswa.tkj@skagata.sch.id',
                'name'            => 'Budi Santoso',
                'password'        => Hash::make('password123'),
                'role'            => 'peminjam',
                'jenis_peminjam'  => 'siswa',
                'bengkel_id'      => $tkj->id,
                'nomor_identitas' => '22.3401.001',
                'nomor_wa'        => '085711223344',
                'status'          => 'aktif',
            ],

            // ── Siswa TKR – Aktif ─────────────────────────────────────
            [
                'email'           => 'siswa.tkr@skagata.sch.id',
                'name'            => 'Rizky Firmansyah',
                'password'        => Hash::make('password123'),
                'role'            => 'peminjam',
                'jenis_peminjam'  => 'siswa',
                'bengkel_id'      => $tkr->id,
                'nomor_identitas' => '22.3402.005',
                'nomor_wa'        => '085799887711',
                'status'          => 'aktif',
            ],

            // ── Guru (tidak terikat bengkel) ──────────────────────────
            [
                'email'           => 'guru@skagata.sch.id',
                'name'            => 'Pak Yono, S.Pd.T.',
                'password'        => Hash::make('password123'),
                'role'            => 'peminjam',
                'jenis_peminjam'  => 'guru',
                'bengkel_id'      => null,
                'nomor_identitas' => '198001012010011001',
                'nomor_wa' => '085699887766',
                'status' => 'aktif',
                'nomor_wa'        => '085699887766',
                'status'          => 'aktif',
            ],

            // 5. Peminjam Siswa Menunggu Approval (Menunggu Acc)
            // ── Siswa TKJ – Menunggu Approval ─────────────────────────
            [
                'email' => 'pending@skagata.sch.id',
                'name' => 'Siti Rahma (Menunggu Acc)',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'jenis_peminjam' => 'siswa',
                'bengkel_id' => 1,
                'nomor_identitas' => '12346',
                'nomor_wa' => '085722334455',
                'status' => 'menunggu_acc',
                'email'           => 'pending@skagata.sch.id',
                'name'            => 'Siti Rahma',
                'password'        => Hash::make('password123'),
                'role'            => 'peminjam',
                'jenis_peminjam'  => 'siswa',
                'bengkel_id'      => $tkj->id,
                'nomor_identitas' => '23.3401.012',
                'nomor_wa'        => '085722334455',
                'status'          => 'menunggu_acc',
            ],

            // 6. Peminjam Siswa Ditangguhkan (Suspend)
            // ── Siswa TKJ – Suspend ───────────────────────────────────
            [
                'email' => 'suspend@skagata.sch.id',
                'name' => 'Rian Pratama (Suspended)',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'jenis_peminjam' => 'siswa',
                'bengkel_id' => 1,
                'nomor_identitas' => '12347',
                'nomor_wa' => '085733445566',
                'status' => 'suspend',
                'email'           => 'suspend@skagata.sch.id',
                'name'            => 'Rian Pratama',
                'password'        => Hash::make('password123'),
                'role'            => 'peminjam',
                'jenis_peminjam'  => 'siswa',
                'bengkel_id'      => $tkj->id,
                'nomor_identitas' => '22.3401.019',
                'nomor_wa'        => '085733445566',
                'status'          => 'suspend',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }
    }
}
