<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('schedule_id')->constrained('distribution_schedules');
            $table->foreignId('staff_id')->constrained('users');
            $table->enum('status', ['completed', 'partial', 'cancelled'])->default('completed')->index();
            $table->timestamp('pickup_time')->index();
            $table->text('notes')->nullable();
            $table->index(['student_id', 'schedule_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_transactions');
    }
};
