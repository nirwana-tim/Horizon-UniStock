<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama periode (contoh: Semester Ganjil 2026)');
            $table->date('start_date')->comment('Tanggal mulai periode');
            $table->date('end_date')->comment('Tanggal akhir periode');
            $table->timestamp('size_change_deadline')->nullable()->comment('Batas akhir input/ubah ukuran');
            $table->boolean('is_active')->default(false)->comment('Apakah periode sedang aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_periods');
    }
};
