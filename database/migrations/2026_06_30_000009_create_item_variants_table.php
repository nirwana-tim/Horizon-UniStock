<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete()->comment('Item induk');
            $table->string('size')->comment('Ukuran (S, M, L, XL, 40, 42, dst)');
            $table->string('sku')->unique()->comment('Stock Keeping Unit');
            $table->decimal('weight', 8, 2)->nullable()->comment('Berat item (opsional)');
            $table->timestamps();

            $table->index(['item_id', 'size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_variants');
    }
};
