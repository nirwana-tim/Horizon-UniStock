<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_departments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique()->comment('Kode departemen (2 digit, contoh: 02, 04)');
            $table->string('name')->comment('Nama departemen/program (contoh: STIKES, STMIK, STIE)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_departments');
    }
};
