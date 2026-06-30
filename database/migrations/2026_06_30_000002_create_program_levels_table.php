<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama level (contoh: Semester 1, Angkatan 2024)');
            $table->string('code')->unique()->comment('Kode level');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_levels');
    }
};
