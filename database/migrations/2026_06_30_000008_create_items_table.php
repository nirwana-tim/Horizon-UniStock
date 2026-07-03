<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama item');
            $table->string('code')->unique()->comment('Kode item (format: KATEGORI-GENDER-TIPE-VARIANT)');
            $table->foreignId('category_id')->constrained('item_categories')->comment('Kategori item');
            $table->string('unit')->default('pcs')->comment('Satuan (pcs, pasang, set)');
            $table->decimal('selling_price', 15, 2)->default(0)->comment('Harga jual per item');
            $table->decimal('hpp', 15, 2)->default(0)->comment('Standard/average HPP per item');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
