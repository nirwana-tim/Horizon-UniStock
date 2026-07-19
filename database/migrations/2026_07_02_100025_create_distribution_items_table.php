<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('distribution_transactions')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items');
            $table->string('expected_size');
            $table->string('actual_size')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('hpp', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->index(['transaction_id', 'item_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_items');
    }
};
