<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique()->comment('Kode tipe (3 karakter, contoh: CLG, CLC, SCB)');
            $table->string('name')->comment('Nama tipe (contoh: College, Clinical, Scrub Suit)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_types');
    }
};
