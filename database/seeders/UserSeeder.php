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
            ['name' => 'Administrator', 'email' => 'admin@unhasy.ac.id', 'role' => 'admin', 'nim_nip' => 'ADM001'],
            ['name' => 'Pustakawan UNHASY', 'email' => 'pustakawan@unhasy.ac.id', 'role' => 'pustakawan', 'nim_nip' => 'PST001'],
            ['name' => 'Kepala Perpustakaan', 'email' => 'kepala@unhasy.ac.id', 'role' => 'kepala_perpustakaan', 'nim_nip' => 'KPP001'],
            ['name' => 'Mahasiswa Demo', 'email' => 'mahasiswa@unhasy.ac.id', 'role' => 'mahasiswa', 'nim_nip' => '2023010001'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                array_merge($user, ['email_verified_at' => now(), 'password' => Hash::make('password'), 'status' => 'active'])
            );
        }
    }
}
