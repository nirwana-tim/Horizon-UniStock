<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_program_id')->constrained('study_programs')->comment('Program studi');
            $table->foreignId('program_level_id')->constrained('program_levels')->comment('Level / angkatan');
            $table->foreignId('period_id')->constrained('distribution_periods')->comment('Periode distribusi');
            $table->enum('student_type', ['freshman', 'continuing'])->default('freshman')->comment('Jenis mahasiswa');
            $table->string('description')->comment('Deskripsi hak barang');
            $table->timestamps();

            $table->unique(['study_program_id', 'program_level_id', 'period_id', 'student_type'], 'entitlement_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entitlements');
    }
};
