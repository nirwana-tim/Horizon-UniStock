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
            $table->string('reference_number')->unique()->comment('Nomor referensi penerimaan');
            $table->foreignId('vendor_id')->constrained('vendors')->comment('Vendor/supplier');
            $table->date('receive_date')->comment('Tanggal penerimaan');
            $table->enum('status', ['pending', 'received', 'cancelled'])->default('pending')->comment('Status');
            $table->text('notes')->nullable()->comment('Catatan penerimaan');
            $table->timestamps();

            $table->index('receive_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_receives');
    }
};
