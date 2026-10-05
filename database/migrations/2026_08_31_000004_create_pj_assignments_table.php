<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pj_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('class_name');
            $table->timestamps();

            $table->unique(['user_id', 'subject_id', 'academic_year_id', 'class_name'], 'pj_user_subject_year_class_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pj_assignments');
    }
};
