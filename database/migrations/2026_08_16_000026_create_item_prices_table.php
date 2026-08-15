<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('hpp', 15, 2)->default(0);
            $table->date('effective_date')->nullable();
            $table->unique(['item_id', 'effective_date']);
            $table->index('effective_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_prices');
    }
};