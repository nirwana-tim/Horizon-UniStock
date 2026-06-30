<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_size_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size_profile_id')->constrained('student_size_profiles')->cascadeOnDelete()->comment('Profil ukuran induk');
            $table->foreignId('item_id')->constrained('items')->comment('Item yg dipilihkan ukuran');
            $table->string('size')->comment('Ukuran yg dipilih student');
            $table->integer('change_count')->default(0)->comment('Jumlah perubahan (maks 1)');
            $table->timestamps();

            $table->unique(['size_profile_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_size_items');
    }
};
