<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_size_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size_item_id')->constrained('student_size_items')->cascadeOnDelete();
            $table->string('old_size');
            $table->string('new_size');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_size_histories');
    }
};
