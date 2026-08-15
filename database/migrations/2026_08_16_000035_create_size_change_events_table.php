<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_change_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->foreignId('faculty_id')->nullable()->constrained('faculties')->nullOnDelete();
            $table->foreignId('study_program_id')->nullable()->constrained('study_programs')->nullOnDelete();
            $table->foreignId('generation_id')->nullable()->constrained('student_generations')->nullOnDelete();
            $table->string('student_level')->nullable();
            $table->unsignedTinyInteger('max_changes')->default(1);
            $table->json('baju_size_options')->nullable();
            $table->json('sepatu_size_options')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_reedit')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index('is_active');
            $table->index(['is_active', 'start_date', 'end_date']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_change_events');
    }
};