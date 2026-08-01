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
        Schema::table('size_change_events', function (Blueprint $table) {
            $table->json('baju_size_options')->nullable()->after('max_changes');
            $table->json('sepatu_size_options')->nullable()->after('baju_size_options');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('size_change_events', function (Blueprint $table) {
            $table->dropColumn(['baju_size_options', 'sepatu_size_options']);
        });
    }
};
