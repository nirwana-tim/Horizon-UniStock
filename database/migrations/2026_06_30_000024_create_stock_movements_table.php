<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->comment('Item bergerak');
            $table->foreignId('variant_id')->constrained('item_variants')->comment('Varian/ukuran');
            $table->enum('type', ['IN', 'OUT'])->comment('Jenis: IN (masuk) / OUT (keluar)');
            $table->integer('quantity')->comment('Jumlah pergerakan');
            $table->string('reference_type')->comment('Tipe referensi (stock_receive/distribution/adjustment)');
            $table->unsignedBigInteger('reference_id')->comment('ID referensi');
            $table->text('notes')->nullable()->comment('Catatan pergerakan');
            $table->timestamps();

            $table->index(['item_id', 'variant_id']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
