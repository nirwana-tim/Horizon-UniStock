<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_receive_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_receive_id')->constrained('stock_receives')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('variant_id')->constrained('item_variants');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('hpp', 15, 2)->default(0);
            $table->index(['stock_receive_id', 'item_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_receive_items');
    }
};
