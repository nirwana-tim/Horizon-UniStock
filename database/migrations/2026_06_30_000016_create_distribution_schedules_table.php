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
            $table->foreignId('stage_id')->constrained('distribution_stages')->comment('Stage distribusi');
            $table->string('name')->comment('Nama jadwal');
            $table->date('date')->comment('Tanggal distribusi');
            $table->string('location')->comment('Lokasi distribusi');
            $table->string('session')->comment('Sesi/jam (contoh: 09:00-12:00)');
            $table->boolean('is_active')->default(true)->comment('Apakah jadwal aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_schedules');
    }
};
