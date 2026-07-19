<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_item_size', function (Blueprint $table) {
            $table->foreignId('item_category_id')->constrained('item_categories')->cascadeOnDelete();
            $table->foreignId('item_size_id')->constrained('item_sizes')->cascadeOnDelete();
            $table->primary(['item_category_id', 'item_size_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_item_size');
    }
};
