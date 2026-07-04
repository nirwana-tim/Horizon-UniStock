<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_categories', function (Blueprint $table) {
            $table->renameColumn('name', 'label');
        });
        Schema::table('item_types', function (Blueprint $table) {
            $table->renameColumn('name', 'label');
        });
        Schema::table('item_departments', function (Blueprint $table) {
            $table->renameColumn('name', 'label');
        });
        Schema::table('item_sizes', function (Blueprint $table) {
            $table->renameColumn('name', 'label');
        });
    }

    public function down(): void
    {
        Schema::table('item_categories', function (Blueprint $table) {
            $table->renameColumn('label', 'name');
        });
        Schema::table('item_types', function (Blueprint $table) {
            $table->renameColumn('label', 'name');
        });
        Schema::table('item_departments', function (Blueprint $table) {
            $table->renameColumn('label', 'name');
        });
        Schema::table('item_sizes', function (Blueprint $table) {
            $table->renameColumn('label', 'name');
        });
    }
};
