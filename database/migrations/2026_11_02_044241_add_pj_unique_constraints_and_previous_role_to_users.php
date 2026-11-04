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
            $table->string('previous_role', 20)->nullable()->after('role');
        });

        // Bersihkan duplikat lama sebelum unique index dipasang:
        // 1) satu user hanya boleh punya satu assignment (simpan yang terbaru)
        // 2) satu tahun ajaran + kelas + mapel hanya boleh punya satu PJ (simpan yang terbaru)
        $keepUserIds = DB::table('pj_assignments')
            ->selectRaw('MAX(id) as id')
            ->groupBy('user_id')
            ->pluck('id');
        DB::table('pj_assignments')
            ->whereNotIn('id', $keepUserIds)
            ->delete();

        $keepComboIds = DB::table('pj_assignments')
            ->selectRaw('MAX(id) as id')
            ->groupBy('academic_year_id', 'class_name', 'subject_id')
            ->pluck('id');
        DB::table('pj_assignments')
            ->whereNotIn('id', $keepComboIds)
            ->delete();

        Schema::table('pj_assignments', function (Blueprint $table) {
            $table->unique('user_id', 'pj_user_unique');
            $table->unique(['academic_year_id', 'class_name', 'subject_id'], 'pj_year_class_subject_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pj_assignments', function (Blueprint $table) {
            $table->dropUnique('pj_user_unique');
            $table->dropUnique('pj_year_class_subject_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('previous_role');
        });
    }
};
