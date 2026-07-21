<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_items', function (Blueprint $table) {
            if (!Schema::hasColumn('distribution_items', 'selling_price_at_distribution')) {
                $table->decimal('selling_price_at_distribution', 15, 2)->default(0)->after('hpp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('distribution_items', function (Blueprint $table) {
            $table->dropColumn('selling_price_at_distribution');
        });
    }
};
