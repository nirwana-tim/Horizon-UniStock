<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_event_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('size_change_events')->cascadeOnDelete();
            $table->unsignedTinyInteger('submission_count')->default(0);
            $table->unique(['student_id', 'event_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_event_submissions');
    }
};