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
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('variant_id')->constrained('item_variants');
            $table->enum('type', ['IN', 'OUT'])->index();
            $table->integer('quantity');
            $table->decimal('hpp', 15, 2)->default(0);
            $table->string('reference_type');
            $table->bigInteger('reference_id');
            $table->text('notes')->nullable();
            $table->index(['item_id', 'variant_id']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
