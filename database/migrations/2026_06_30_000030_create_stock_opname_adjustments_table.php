<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->comment('Batch opname terkait');
            $table->foreignId('stock_movement_id')->nullable()->constrained('stock_movements')->nullOnDelete()->comment('Stock movement hasil adjustment');
            $table->enum('type', ['surplus', 'shortage'])->comment('Jenis penyesuaian');
            $table->integer('quantity')->comment('Jumlah penyesuaian');
            $table->text('reason')->nullable()->comment('Alasan penyesuaian');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->comment('User yang menyetujui');
            $table->timestamp('approved_at')->nullable()->comment('Waktu persetujuan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_adjustments');
    }
};
