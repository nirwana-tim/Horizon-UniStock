<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->char('gender', 1)->nullable()->after('code')->comment('Gender (L/P/U)');
            $table->foreignId('type_id')->nullable()->after('gender')->constrained('item_types')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('type_id')->constrained('item_departments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['gender', 'type_id', 'department_id']);
        });
    }
};
