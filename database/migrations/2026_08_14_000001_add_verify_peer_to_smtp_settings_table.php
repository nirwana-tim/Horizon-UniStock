<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smtp_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('smtp_settings', 'verify_peer')) {
                $table->boolean('verify_peer')->default(false)->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('smtp_settings', function (Blueprint $table) {
            if (Schema::hasColumn('smtp_settings', 'verify_peer')) {
                $table->dropColumn('verify_peer');
            }
        });
    }
};