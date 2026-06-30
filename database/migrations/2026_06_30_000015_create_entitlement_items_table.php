<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entitlement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entitlement_id')->constrained('entitlements')->cascadeOnDelete()->comment('Entitlement induk');
            $table->foreignId('item_id')->constrained('items')->comment('Item yang diberikan');
            $table->integer('quantity')->default(1)->comment('Jumlah item yang diberikan');
            $table->timestamps();

            $table->unique(['entitlement_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entitlement_items');
    }
};
