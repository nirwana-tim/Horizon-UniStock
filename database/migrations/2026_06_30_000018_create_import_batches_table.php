<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('import_type')->comment('Tipe import (students/eligible/items/stock_opname/item_master)');
            $table->string('file_name')->comment('Nama file yang diupload');
            $table->integer('total_rows')->comment('Total baris dalam file');
            $table->integer('success_rows')->default(0)->comment('Baris berhasil');
            $table->integer('failed_rows')->default(0)->comment('Baris gagal');
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing')->comment('Status');
            $table->json('error_log')->nullable()->comment('Log error per baris');
            $table->foreignId('imported_by')->constrained('users')->comment('User yang import');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
