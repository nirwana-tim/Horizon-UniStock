<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_receive_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_receive_id')->constrained('stock_receives')->cascadeOnDelete()->comment('Penerimaan induk');
            $table->foreignId('item_id')->constrained('items')->comment('Item yang diterima');
            $table->foreignId('variant_id')->constrained('item_variants')->comment('Varian/ukuran');
            $table->integer('quantity')->comment('Jumlah yang diterima');
            $table->decimal('unit_price', 15, 2)->default(0)->comment('Harga satuan');
            $table->decimal('hpp', 15, 2)->default(0)->comment('HPP per batch');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_receive_items');
    }
};
