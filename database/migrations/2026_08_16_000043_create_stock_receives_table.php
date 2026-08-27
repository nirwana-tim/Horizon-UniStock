<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_receives', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->date('receive_date');
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->index('receive_date');
            $table->index('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_receives');
    }
};
