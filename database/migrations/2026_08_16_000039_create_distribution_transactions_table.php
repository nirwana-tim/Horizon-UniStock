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
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('distribution_schedules')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('completed');
            $table->timestamp('pickup_time');
            $table->text('notes')->nullable();
            $table->index('status');
            $table->index('pickup_time');
            $table->index('staff_id');
            $table->index(['student_id', 'schedule_id', 'status']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_transactions');
    }
};
