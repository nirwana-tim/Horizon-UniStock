<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama vendor/supplier');
            $table->string('email')->nullable()->comment('Email vendor');
            $table->string('contact')->nullable()->comment('Nama kontak person');
            $table->string('phone')->nullable()->comment('No telepon vendor');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
