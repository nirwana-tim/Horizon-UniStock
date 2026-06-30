<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->comment('User yang melakukan aksi');
            $table->string('action')->comment('Jenis aksi (create/update/delete/login/export)');
            $table->string('model_type')->comment('Model yang terpengaruh');
            $table->unsignedBigInteger('model_id')->comment('ID model yang terpengaruh');
            $table->json('old_values')->nullable()->comment('Data sebelum perubahan');
            $table->json('new_values')->nullable()->comment('Data setelah perubahan');
            $table->string('ip_address', 45)->nullable()->comment('IP address user');
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
