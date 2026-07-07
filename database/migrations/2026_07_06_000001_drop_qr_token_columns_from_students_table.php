<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'qr_token')) {
                $table->dropColumn('qr_token');
            }
            if (Schema::hasColumn('students', 'qr_generated_at')) {
                $table->dropColumn('qr_generated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('qr_token')->unique()->nullable();
            $table->timestamp('qr_generated_at')->nullable();
        });
    }
};
