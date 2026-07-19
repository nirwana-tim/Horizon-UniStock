<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('import_type');
            $table->string('file_name');
            $table->integer('total_rows');
            $table->integer('success_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->json('error_log')->nullable();
            $table->foreignId('imported_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
