<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_size_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete()->comment('Mahasiswa terkait');
            $table->foreignId('period_id')->constrained('distribution_periods')->comment('Periode distribusi');
            $table->boolean('is_filled')->default(false)->comment('Apakah sudah isi ukuran');
            $table->timestamp('filled_at')->nullable()->comment('Waktu pertama kali isi ukuran');
            $table->timestamps();

            $table->unique(['student_id', 'period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_size_profiles');
    }
};
