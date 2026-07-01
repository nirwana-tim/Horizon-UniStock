<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete()->comment('Item terkait');
            $table->foreignId('period_id')->nullable()->constrained('distribution_periods')->nullOnDelete()->comment('Perode harga (null=current)');
            $table->decimal('selling_price', 15, 2)->default(0)->comment('Harga jual per periode');
            $table->decimal('hpp', 15, 2)->default(0)->comment('Harga pokok pembelian per periode');
            $table->date('effective_date')->nullable()->comment('Tanggal efektif harga');
            $table->timestamps();

            $table->unique(['item_id', 'period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_prices');
    }
};
