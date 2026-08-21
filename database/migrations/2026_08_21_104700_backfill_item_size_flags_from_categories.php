<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('item_sizes', 'is_baju')) {
            return;
        }

        $bajuCatIds = DB::table('item_categories')
            ->whereIn('code', ['UNF', 'KIT', 'KTM'])
            ->pluck('id');

        $sepatuCatIds = DB::table('item_categories')
            ->where('code', 'SHO')
            ->pluck('id');

        if ($bajuCatIds->isNotEmpty()) {
            $bajuSizeIds = DB::table('category_item_size')
                ->whereIn('item_category_id', $bajuCatIds)
                ->pluck('item_size_id')
                ->unique();

            if ($bajuSizeIds->isNotEmpty()) {
                DB::table('item_sizes')
                    ->whereIn('id', $bajuSizeIds)
                    ->update(['is_baju' => true]);
            }
        }

        if ($sepatuCatIds->isNotEmpty()) {
            $sepatuSizeIds = DB::table('category_item_size')
                ->whereIn('item_category_id', $sepatuCatIds)
                ->pluck('item_size_id')
                ->unique();

            if ($sepatuSizeIds->isNotEmpty()) {
                DB::table('item_sizes')
                    ->whereIn('id', $sepatuSizeIds)
                    ->update(['is_sepatu' => true]);
            }
        }
    }

    public function down(): void
    {
        DB::table('item_sizes')
            ->where('is_baju', true)
            ->orWhere('is_sepatu', true)
            ->update(['is_baju' => false, 'is_sepatu' => false]);
    }
};
