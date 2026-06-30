<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->comment('Item');
            $table->foreignId('variant_id')->constrained('item_variants')->comment('Varian/ukuran');
            $table->integer('quantity')->default(0)->comment('Saldo stok tersedia');
            $table->integer('reserved')->default(0)->comment('Jumlah stok di-reserve');
            $table->decimal('last_hpp', 15, 2)->default(0)->comment('HPP terakhir');
            $table->timestamps();

            $table->unique(['item_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
    }
};
