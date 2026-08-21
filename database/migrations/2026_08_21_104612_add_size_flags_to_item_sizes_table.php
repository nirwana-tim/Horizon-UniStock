<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_sizes', function (Blueprint $table) {
            $table->boolean('is_baju')->default(false)->after('label');
            $table->boolean('is_sepatu')->default(false)->after('is_baju');
        });
    }

    public function down(): void
    {
        Schema::table('item_sizes', function (Blueprint $table) {
            $table->dropColumn(['is_baju', 'is_sepatu']);
        });
    }
};
