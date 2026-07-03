<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->string('email');
            $table->string('code', 6);
            $table->string('type')->default('password_reset');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index('nim');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
