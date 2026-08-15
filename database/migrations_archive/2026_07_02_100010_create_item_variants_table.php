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
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('size_id')->nullable()->constrained('item_sizes')->nullOnDelete();
            $table->string('size');
            $table->string('size_label')->nullable();
            $table->string('sku')->unique();
            $table->decimal('weight', 8, 2)->nullable();
            $table->index(['item_id', 'size']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_variants');
    }
};
