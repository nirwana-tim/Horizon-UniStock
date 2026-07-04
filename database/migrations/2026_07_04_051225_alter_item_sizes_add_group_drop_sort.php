<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_sizes', function (Blueprint $table) {
            $table->string('size_group', 20)->default('uniform')->after('name');
            $table->dropColumn('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('item_sizes', function (Blueprint $table) {
            $table->integer('sort_order')->default(0);
            $table->dropColumn('size_group');
        });
    }
};
