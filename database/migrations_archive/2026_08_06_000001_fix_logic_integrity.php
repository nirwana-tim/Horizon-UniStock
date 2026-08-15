<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->enum('status', ['draft', 'counted', 'approved'])->default('draft')->change();
        });

        Schema::table('distribution_transactions', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropIndex(['student_id', 'schedule_id']);
            $table->unique(['student_id', 'schedule_id']);
            $table->foreign('student_id')->references('id')->on('students');
        });
    }

    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->enum('status', ['draft', 'completed', 'adjusted'])->default('draft')->change();
        });

        Schema::table('distribution_transactions', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropUnique(['student_id', 'schedule_id']);
            $table->index(['student_id', 'schedule_id']);
            $table->foreign('student_id')->references('id')->on('students');
        });
    }
};
