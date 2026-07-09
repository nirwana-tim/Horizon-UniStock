<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->supportsFullTextIndexes()) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            $table->fullText(['name', 'nim']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->fullText('name');
        });

        Schema::table('faculties', function (Blueprint $table) {
            $table->fullText('name');
        });

        Schema::table('study_programs', function (Blueprint $table) {
            $table->fullText('name');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->fullText('name');
        });

        Schema::table('item_categories', function (Blueprint $table) {
            $table->fullText(['code', 'label']);
        });

        Schema::table('item_types', function (Blueprint $table) {
            $table->fullText(['code', 'label']);
        });

        Schema::table('item_sizes', function (Blueprint $table) {
            $table->fullText('label');
        });

        Schema::table('item_departments', function (Blueprint $table) {
            $table->fullText('label');
        });
    }

    public function down(): void
    {
        if (! $this->supportsFullTextIndexes()) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            $table->dropFullText(['name', 'nim']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropFullText('name');
        });

        Schema::table('faculties', function (Blueprint $table) {
            $table->dropFullText('name');
        });

        Schema::table('study_programs', function (Blueprint $table) {
            $table->dropFullText('name');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropFullText('name');
        });

        Schema::table('item_categories', function (Blueprint $table) {
            $table->dropFullText(['code', 'label']);
        });

        Schema::table('item_types', function (Blueprint $table) {
            $table->dropFullText(['code', 'label']);
        });

        Schema::table('item_sizes', function (Blueprint $table) {
            $table->dropFullText('label');
        });

        Schema::table('item_departments', function (Blueprint $table) {
            $table->dropFullText('label');
        });
    }

    private function supportsFullTextIndexes(): bool
    {
        return in_array(Schema::getConnection()->getDriverName(), ['mysql', 'pgsql'], true);
    }
};
