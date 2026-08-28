<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('base_code', 50)->nullable();
            $table->char('gender', 1)->nullable();
            $table->foreignId('category_id')->nullable()->constrained('item_categories')->nullOnDelete();
            $table->foreignId('type_id')->nullable()->constrained('item_types')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('item_departments')->nullOnDelete();
            $table->string('unit')->default('pcs');
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('hpp', 15, 2)->default(0);
            $table->timestamps();
            $table->boolean('is_active')->default(true);
            $table->index('name');
            $table->index('base_code');
            $table->index('is_active');
            $table->index('gender');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
