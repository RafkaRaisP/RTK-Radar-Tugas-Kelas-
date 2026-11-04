<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        // Tiga tahun ajaran sesuai angkatan:
        //   X  = angkatan 26/27 (NIS 2627...) — tahun ajaran berjalan
        //   XI = angkatan 25/26 (NIS 2526...)
        //   XII = angkatan 24/25 (NIS 2425...)
        $years = [
            ['name' => '2026/2027', 'is_active' => true],
            ['name' => '2025/2026', 'is_active' => false],
            ['name' => '2024/2025', 'is_active' => false],
        ];

        foreach ($years as $year) {
            AcademicYear::updateOrCreate(['name' => $year['name']], [
                'is_active' => $year['is_active'],
            ]);
        }

        // Pastikan hanya satu tahun yang aktif
        $active = AcademicYear::where('name', '2026/2027')->value('id');
        if ($active) {
            AcademicYear::where('id', '!=', $active)->update(['is_active' => false]);
        }
    }
}
