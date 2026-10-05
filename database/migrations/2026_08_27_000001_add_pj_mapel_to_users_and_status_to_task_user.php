<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pj_subject', 100)->nullable()->after('role');
        });

        Schema::table('task_user', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('task_id');
            $table->foreignId('confirmed_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('task_user', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by']);
            $table->dropColumn(['status', 'confirmed_by']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pj_subject');
        });
    }
};
