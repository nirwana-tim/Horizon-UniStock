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
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('variant_id')->constrained('item_variants');
            $table->integer('system_quantity');
            $table->integer('physical_quantity');
            $table->integer('computed_variance')->virtualAs('physical_quantity - system_quantity');
            $table->text('notes')->nullable();
            $table->unique(['stock_opname_id', 'item_id', 'variant_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
