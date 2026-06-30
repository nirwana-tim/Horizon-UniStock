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
            $table->foreignId('student_id')->constrained('students')->comment('Mahasiswa yang mengambil');
            $table->foreignId('schedule_id')->constrained('distribution_schedules')->comment('Jadwal distribusi');
            $table->foreignId('stage_id')->constrained('distribution_stages')->comment('Stage distribusi');
            $table->foreignId('staff_id')->constrained('users')->comment('Staff yang melayani');
            $table->enum('status', ['completed', 'partial', 'cancelled'])->default('completed')->comment('Status');
            $table->timestamp('pickup_time')->comment('Waktu pengambilan');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->timestamps();

            $table->index(['student_id', 'schedule_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_transactions');
    }
};
