<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('faculties', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('study_programs', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('program_levels', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('distribution_stages', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('distribution_periods', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('entitlements', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('faculties', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('study_programs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('program_levels', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('distribution_stages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('distribution_periods', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('entitlements', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
