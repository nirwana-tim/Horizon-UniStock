<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('distribution_periods')->comment('Periode induk');
            $table->string('name')->comment('Nama stage (contoh: Tahap 1, Gelombang A)');
            $table->integer('stage_order')->comment('Urutan stage');
            $table->date('start_date')->comment('Tanggal mulai stage');
            $table->date('end_date')->comment('Tanggal akhir stage');
            $table->text('notes')->nullable()->comment('Catatan stage');
            $table->timestamps();

            $table->index(['period_id', 'stage_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_stages');
    }
};
