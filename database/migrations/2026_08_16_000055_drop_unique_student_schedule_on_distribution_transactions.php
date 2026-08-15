<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_transactions', function (Blueprint $table) {
            $table->dropUnique('distribution_transactions_student_id_schedule_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('distribution_transactions', function (Blueprint $table) {
            $table->unique(['student_id', 'schedule_id']);
        });
    }
};
