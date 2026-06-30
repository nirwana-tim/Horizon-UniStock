<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dist_schedule_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('distribution_schedules')->cascadeOnDelete()->comment('Jadwal distribusi');
            $table->foreignId('item_id')->constrained('items')->comment('Item yang dibagikan');
            $table->timestamps();

            $table->unique(['schedule_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dist_schedule_items');
    }
};
