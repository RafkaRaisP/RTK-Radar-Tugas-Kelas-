<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['username' => 'ketua_rpl1'], [
            'name' => 'Ketua Kelas XI RPL 1',
            'email' => 'ketua_rpl1@example.com',
            'password' => Hash::make('password123'),
            'role' => 'ketua_kelas',
            'class_name' => 'XI RPL 1',
        ]);

        User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'class_name' => 'XI RPL 1',
        ]);
    }
}
