<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_schedules', function (Blueprint $table) {
            $table->foreignId('stage_id')->nullable()->after('semester')
                ->constrained('distribution_stages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('distribution_schedules', function (Blueprint $table) {
            $table->dropForeign(['stage_id']);
            $table->dropColumn('stage_id');
        });
    }
};
