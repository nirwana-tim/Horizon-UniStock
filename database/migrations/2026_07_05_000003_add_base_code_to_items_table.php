<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('base_code', 50)->nullable()->after('code')->index();
        });

        // Populate base_code by stripping the last size suffix from code
        // Format: KATEGORI-GENDER-TIPE-VARIANT-SIZE → base_code = KATEGORI-GENDER-TIPE-VARIANT
        $items = DB::table('items')->select('id', 'code')->get();
        foreach ($items as $item) {
            $parts = explode('-', $item->code);
            array_pop($parts); // remove size suffix
            $baseCode = implode('-', $parts);
            DB::table('items')->where('id', $item->id)->update(['base_code' => $baseCode]);
        }
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('base_code');
        });
    }
};
