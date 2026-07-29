<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Ketua Kelas XI RPL 1',
            'username' => 'ketua_rpl1',
            'email' => 'ketua_rpl1@example.com',
            'password' => Hash::make('password123'),
            'role' => 'ketua_kelas',
            'class_name' => 'XI RPL 1',
        ]);
    }
}
