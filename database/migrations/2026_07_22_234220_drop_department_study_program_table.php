<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('department_study_program');
    }

    public function down(): void
    {
        Schema::create('department_study_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('item_departments')->cascadeOnDelete();
            $table->foreignId('study_program_id')->constrained('study_programs')->cascadeOnDelete();
            $table->unique(['department_id', 'study_program_id']);
            $table->timestamps();
        });
    }
};
