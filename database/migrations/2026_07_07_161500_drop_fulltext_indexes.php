<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', fn(Blueprint $t) => $t->dropFullText(['name', 'nim']));
        Schema::table('items', function (Blueprint $t) {
            $t->dropFullText(['name']);
            $t->dropFullText(['code', 'base_code']);
        });
        Schema::table('faculties', fn(Blueprint $t) => $t->dropFullText(['name']));
        Schema::table('study_programs', fn(Blueprint $t) => $t->dropFullText(['name']));
        Schema::table('vendors', function (Blueprint $t) {
            $t->dropFullText(['name']);
            $t->dropFullText(['email', 'contact']);
        });
        Schema::table('item_categories', fn(Blueprint $t) => $t->dropFullText(['code', 'label']));
        Schema::table('item_types', fn(Blueprint $t) => $t->dropFullText(['code', 'label']));
        Schema::table('item_sizes', fn(Blueprint $t) => $t->dropFullText(['label']));
        Schema::table('item_departments', fn(Blueprint $t) => $t->dropFullText(['label']));
    }

    public function down(): void
    {
        Schema::table('students', fn(Blueprint $t) => $t->fullText(['name', 'nim']));
        Schema::table('items', function (Blueprint $t) {
            $t->fullText('name');
            $t->fullText(['code', 'base_code']);
        });
        Schema::table('faculties', fn(Blueprint $t) => $t->fullText('name'));
        Schema::table('study_programs', fn(Blueprint $t) => $t->fullText('name'));
        Schema::table('vendors', function (Blueprint $t) {
            $t->fullText('name');
            $t->fullText(['email', 'contact']);
        });
        Schema::table('item_categories', fn(Blueprint $t) => $t->fullText(['code', 'label']));
        Schema::table('item_types', fn(Blueprint $t) => $t->fullText(['code', 'label']));
        Schema::table('item_sizes', fn(Blueprint $t) => $t->fullText('label'));
        Schema::table('item_departments', fn(Blueprint $t) => $t->fullText('label'));
    }
};
