<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_item_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_category_id')->constrained('item_categories')->cascadeOnDelete();
            $table->foreignId('item_type_id')->constrained('item_types')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['item_category_id', 'item_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_item_type');
    }
};
