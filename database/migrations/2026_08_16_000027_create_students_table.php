<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nim')->unique();
            $table->string('name');
            $table->char('gender', 1)->nullable();
            $table->string('email_kampus')->nullable()->unique();
            $table->string('email_pribadi')->nullable();
            $table->foreignId('study_program_id')->constrained('study_programs');
            $table->foreignId('generation_id')->constrained('student_generations');
            $table->string('student_level', 50)->default('Y1S1');
            $table->string('status', 20)->default('active');
            $table->string('current_semester', 20)->default('Y1S1');
            $table->string('entitlement_code')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('name');
            $table->index('student_level');
            $table->index('entitlement_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
