<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete()->comment('Batch opname induk');
            $table->foreignId('item_id')->constrained('items')->comment('Item yang diopname');
            $table->foreignId('variant_id')->constrained('item_variants')->comment('Varian/ukuran');
            $table->integer('system_quantity')->comment('Stok menurut sistem');
            $table->integer('physical_quantity')->comment('Stok menurut hitung fisik');
            $table->integer('computed_variance')->virtualAs('physical_quantity - system_quantity')->comment('Selisih (computed)');
            $table->text('notes')->nullable()->comment('Catatan per item');
            $table->timestamps();

            $table->unique(['stock_opname_id', 'item_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
