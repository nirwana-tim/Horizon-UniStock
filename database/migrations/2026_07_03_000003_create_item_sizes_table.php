<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('Kode ukuran (contoh: 03, 04, S, M, L, XL)');
            $table->string('name')->comment('Label ukuran (contoh: S, M, L, XL, 37, 38, All Size)');
            $table->integer('sort_order')->default(0)->comment('Urutan tampilan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_sizes');
    }
};
