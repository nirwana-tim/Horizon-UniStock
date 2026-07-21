<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('students', 'status')) {
            Schema::table('students', function (Blueprint $table) {
                $table->enum('status', ['active', 'leave', 'graduated', 'non_active'])->default('active')->after('student_type')->index();
            });
        }

        if (!Schema::hasTable('size_change_events')) {
            Schema::create('size_change_events', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->dateTime('start_date');
                $table->dateTime('end_date');
                $table->foreignId('faculty_id')->nullable()->constrained('faculties')->nullOnDelete();
                $table->foreignId('study_program_id')->nullable()->constrained('study_programs')->nullOnDelete();
                $table->foreignId('program_level_id')->nullable()->constrained('program_levels')->nullOnDelete();
                $table->string('student_type')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('allow_reedit')->default(false);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('size_change_events');
        if (Schema::hasColumn('students', 'status')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
