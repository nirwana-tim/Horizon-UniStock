<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('student_size_histories');
        Schema::create('student_size_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size_item_id')->constrained('student_size_items')->cascadeOnDelete()->comment('Item ukuran terkait');
            $table->string('old_size')->comment('Ukuran sebelum diubah');
            $table->string('new_size')->comment('Ukuran setelah diubah');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete()->comment('Staff yang ubah (null = student)');
            $table->timestamp('changed_at')->comment('Waktu perubahan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_size_histories');
    }
};
