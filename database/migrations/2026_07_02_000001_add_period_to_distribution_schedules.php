<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_schedules', function (Blueprint $table) {
            $table->string('period')->nullable()->after('name')->comment('Periode distribusi (contoh: 2025/2026)');
        });
    }

    public function down(): void
    {
        Schema::table('distribution_schedules', function (Blueprint $table) {
            $table->dropColumn('period');
        });
    }
};
