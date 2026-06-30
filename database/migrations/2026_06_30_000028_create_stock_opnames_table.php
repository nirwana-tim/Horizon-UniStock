<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique()->comment('Nomor referensi opname');
            $table->date('opname_date')->comment('Tanggal opname');
            $table->string('period')->comment('Periode opname (contoh: Agustus 2026)');
            $table->text('notes')->nullable()->comment('Catatan opname');
            $table->enum('status', ['draft', 'completed', 'adjusted'])->default('draft')->comment('Status');
            $table->foreignId('created_by')->constrained('users')->comment('User yang membuat batch');
            $table->timestamps();

            $table->index('opname_date');
            $table->index('period');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
