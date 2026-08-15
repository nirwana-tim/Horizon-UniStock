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
            $table->string('name')->index();
            $table->string('code')->unique();
            $table->string('base_code', 50)->nullable()->index();
            $table->char('gender', 1)->nullable();
            $table->foreignId('category_id')->constrained('item_categories');
            $table->foreignId('type_id')->nullable()->constrained('item_types')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('item_departments')->nullOnDelete();
            $table->string('unit')->default('pcs');
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('hpp', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
