<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('entitlement_items');
        Schema::dropIfExists('entitlements');

        Schema::create('entitlements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Kode grup: {LevelCode}{FacultyCode}{ProdiCode}');
            $table->string('description')->nullable()->comment('Deskripsi entitlement');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('entitlement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entitlement_id')->constrained('entitlements')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->unique(['entitlement_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entitlement_items');
        Schema::dropIfExists('entitlements');
    }
};
