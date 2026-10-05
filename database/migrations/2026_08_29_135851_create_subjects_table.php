<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Backfill dari data yang sudah ada + daftar mapel default.
        $existing = DB::table('tasks')->select('subject')->distinct()
            ->union(DB::table('users')->select('pj_subject')->whereNotNull('pj_subject'))
            ->pluck('subject')
            ->filter()
            ->values()
            ->all();

        $defaults = ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'PKK', 'PJOK', 'Agama', 'Sejarah', 'PKN', 'Seni Budaya', 'Informatika'];

        $names = array_values(array_unique(array_filter(array_merge($existing, $defaults))));
        $now = now();
        foreach ($names as $name) {
            if (! DB::table('subjects')->where('name', $name)->exists()) {
                DB::table('subjects')->insert(['name' => $name, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
