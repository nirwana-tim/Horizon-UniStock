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
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->index();
            $table->string('nim')->unique();
            $table->string('name')->index();
            $table->string('email_kampus')->unique();
            $table->string('email_pribadi')->nullable();
            $table->foreignId('study_program_id')->constrained('study_programs');
            $table->foreignId('generation_id')->constrained('student_generations');
            $table->string('student_level', 50)->nullable()->index();
            $table->string('entitlement_code')->nullable()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
