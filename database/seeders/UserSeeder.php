<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        User::create([
            'name' => 'Admin Sarpras',
            'email' => 'admin@skagata.com',
            'password' => Hash::make('secret12'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User Sekolah',
            'email' => 'user@skagata.com',
            'password' => Hash::make('secret12'),
            'role' => 'user',
        ]);
    }
}
