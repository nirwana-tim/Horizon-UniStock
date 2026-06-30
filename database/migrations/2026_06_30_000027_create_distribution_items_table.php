<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('distribution_transactions')->cascadeOnDelete()->comment('Transaksi induk');
            $table->foreignId('item_id')->constrained('items')->comment('Item yang diambil');
            $table->string('expected_size')->comment('Ukuran yang diinput mahasiswa');
            $table->string('actual_size')->nullable()->comment('Ukuran yang benar-benar diberikan');
            $table->integer('quantity')->default(1)->comment('Jumlah item yang diberikan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_items');
    }
};
