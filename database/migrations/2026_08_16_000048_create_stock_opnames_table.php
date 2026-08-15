<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->date('opname_date');
            $table->string('period');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'counted', 'approved'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->index('opname_date');
            $table->index('period');
            $table->index('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};