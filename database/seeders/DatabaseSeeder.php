<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $yearId = AcademicYear::currentId();

        // Admin
        User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'class_name' => 'XI RPL 1',
            'academic_year_id' => $yearId,
        ]);

        // Ketua Kelas
        $ketua = [
            ['username' => 'ketua_rpl1',        'name' => 'Ketua Kelas XI RPL 1',        'class_name' => 'XI RPL 1',        'password' => '77ce0684'],
            ['username' => 'ketua_rpl2',        'name' => 'Ketua Kelas XI RPL 2',        'class_name' => 'XI RPL 2',        'password' => '97f02bc4'],
            ['username' => 'ketua_rpl3',        'name' => 'Ketua Kelas XI RPL 3',        'class_name' => 'XI RPL 3',        'password' => 'b006c51b'],
            ['username' => 'ketua_rpl4',        'name' => 'Ketua Kelas XI RPL 4',        'class_name' => 'XI RPL 4',        'password' => '7596feb8'],
            ['username' => 'ketua_tkj1',        'name' => 'Ketua Kelas XI TKJ 1',        'class_name' => 'XI TKJ 1',        'password' => '1109c771'],
            ['username' => 'ketua_tkj2',        'name' => 'Ketua Kelas XI TKJ 2',        'class_name' => 'XI TKJ 2',        'password' => 'a53960f1'],
            ['username' => 'ketua_dkv1',        'name' => 'Ketua Kelas XI DKV 1',        'class_name' => 'XI DKV 1',        'password' => 'c90f4379'],
            ['username' => 'ketua_dkv2',        'name' => 'Ketua Kelas XI DKV 2',        'class_name' => 'XI DKV 2',        'password' => '1d763b0f'],
            ['username' => 'ketua_transmisi1',  'name' => 'Ketua Kelas XI Transmisi 1',  'class_name' => 'XI Transmisi 1',  'password' => '45ce8663'],
        ];

        foreach ($ketua as $u) {
            User::updateOrCreate(['username' => $u['username']], [
                'name'       => $u['name'],
                'email'      => $u['username'] . '@example.com',
                'password'   => Hash::make($u['password']),
                'role'       => 'ketua_kelas',
                'class_name' => $u['class_name'],
                'academic_year_id' => $yearId,
            ]);
        }

        // Sekretaris
        $sekre = [
            ['username' => 'sekretaris_rpl1',        'name' => 'Sekretaris XI RPL 1',        'class_name' => 'XI RPL 1',        'password' => 'ad7d2509'],
            ['username' => 'sekretaris_rpl2',        'name' => 'Sekretaris XI RPL 2',        'class_name' => 'XI RPL 2',        'password' => '90be026e'],
            ['username' => 'sekretaris_rpl3',        'name' => 'Sekretaris XI RPL 3',        'class_name' => 'XI RPL 3',        'password' => 'a8e5a428'],
            ['username' => 'sekretaris_rpl4',        'name' => 'Sekretaris XI RPL 4',        'class_name' => 'XI RPL 4',        'password' => '83f787a1'],
            ['username' => 'sekretaris_tkj1',        'name' => 'Sekretaris XI TKJ 1',        'class_name' => 'XI TKJ 1',        'password' => '3f059d02'],
            ['username' => 'sekretaris_tkj2',        'name' => 'Sekretaris XI TKJ 2',        'class_name' => 'XI TKJ 2',        'password' => '5bec118f'],
            ['username' => 'sekretaris_dkv1',        'name' => 'Sekretaris XI DKV 1',        'class_name' => 'XI DKV 1',        'password' => '266c7e31'],
            ['username' => 'sekretaris_dkv2',        'name' => 'Sekretaris XI DKV 2',        'class_name' => 'XI DKV 2',        'password' => '98811d29'],
            ['username' => 'sekretaris_transmisi1',  'name' => 'Sekretaris XI Transmisi 1',  'class_name' => 'XI Transmisi 1',  'password' => 'ca755d21'],
        ];

        foreach ($sekre as $u) {
            User::updateOrCreate(['username' => $u['username']], [
                'name'       => $u['name'],
                'email'      => $u['username'] . '@example.com',
                'password'   => Hash::make($u['password']),
                'role'       => 'sekretaris',
                'class_name' => $u['class_name'],
                'academic_year_id' => $yearId,
            ]);
        }

        // PJ Mapel (contoh: 1 PJ per mapel utama di kelas XI RPL 1)
        User::updateOrCreate(['username' => 'pj_matematika'], [
            'name'       => 'PJ Matematika',
            'email'      => 'pj_matematika@example.com',
            'password'   => Hash::make('pj123456'),
            'role'       => 'pj_mapel',
            'class_name' => 'XI RPL 1',
            'pj_subject' => 'Matematika',
            'academic_year_id' => $yearId,
        ]);

        User::updateOrCreate(['username' => 'pj_bahasa'], [
            'name'       => 'PJ Bahasa Indonesia',
            'email'      => 'pj_bahasa@example.com',
            'password'   => Hash::make('pj123456'),
            'role'       => 'pj_mapel',
            'class_name' => 'XI RPL 1',
            'pj_subject' => 'Bahasa Indonesia',
            'academic_year_id' => $yearId,
        ]);
    }
}
