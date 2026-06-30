<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Relasi ke akun login');
            $table->string('nim')->unique()->comment('Nomor Induk Mahasiswa');
            $table->string('name')->comment('Nama lengkap mahasiswa');
            $table->string('email_kampus')->unique()->comment('Email kampus @krw.horizon.ac.id');
            $table->string('email_pribadi')->nullable()->comment('Email pribadi mahasiswa');
            $table->string('qr_token')->unique()->nullable()->comment('Token QR permanen');
            $table->timestamp('qr_generated_at')->nullable()->comment('Waktu QR pertama kali digenerate');
            $table->foreignId('study_program_id')->constrained('study_programs')->comment('Program studi');
            $table->foreignId('program_level_id')->constrained('program_levels')->comment('Level / angkatan');
            $table->enum('student_type', ['freshman', 'continuing'])->default('freshman')->comment('Jenis mahasiswa');
            $table->timestamp('email_verified_at')->nullable()->comment('Waktu email kampus terverifikasi');
            $table->timestamps();

            $table->index('student_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
