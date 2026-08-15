<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->integer('total_transactions')->default(0);
            $table->integer('total_items_received')->default(0);
            $table->decimal('total_spend', 15, 2)->default(0);
            $table->timestamp('last_distribution_at')->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->index('last_distribution_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_summaries');
    }
};