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
        if (! Schema::hasIndex('distribution_transactions', 'distribution_transactions_student_id_schedule_id_unique')) {
            Schema::table('distribution_transactions', function (Blueprint $table) {
                $table->unique(['student_id', 'schedule_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('distribution_transactions', 'distribution_transactions_student_id_schedule_id_unique')) {
            Schema::table('distribution_transactions', function (Blueprint $table) {
                $table->dropUnique(['student_id', 'schedule_id']);
            });
        }
    }
};
