<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('period')->nullable()->index();
            $table->string('semester', 10)->nullable();
            $table->string('student_level')->nullable()->index();
            $table->date('date')->index();
            $table->string('location');
            $table->string('session');
            $table->foreignId('generation_id')->nullable()->constrained('student_generations')->nullOnDelete();
            $table->foreignId('faculty_id')->nullable()->constrained('faculties')->nullOnDelete();
            $table->foreignId('study_program_id')->nullable()->constrained('study_programs')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_schedules');
    }
};
