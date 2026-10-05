<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('class_name')
                ->constrained('academic_years')->nullOnDelete();
        });

        // Backfill: semua user yang sudah ada masuk tahun ajaran aktif (atau tahun pertama jika belum ada yang aktif)
        $yearId = DB::table('academic_years')->where('is_active', 1)->value('id')
            ?? DB::table('academic_years')->value('id');
        if ($yearId) {
            DB::table('users')->update(['academic_year_id' => $yearId]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('academic_year_id');
        });
    }
};