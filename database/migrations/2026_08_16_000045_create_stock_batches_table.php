<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('item_variants')->nullOnDelete();
            $table->integer('quantity_remaining')->default(0);
            $table->decimal('unit_hpp', 15, 2)->default(0);
            $table->date('received_date');
            $table->foreignId('stock_receive_item_id')->nullable()->constrained('stock_receive_items')->nullOnDelete();
            $table->index(['item_id', 'variant_id']);
            $table->index(['item_id', 'variant_id', 'received_date']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
