<?php

use App\Models\ItemCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_categories', function (Blueprint $table) {
            $table->string('code', 3)->nullable()->after('id')->comment('Kode kategori (3 karakter)');
            $table->softDeletes();
        });

        ItemCategory::where('name', 'Almamater')->update(['code' => 'ALM']);
        ItemCategory::where('name', 'Seragam Kuliah')->update(['code' => 'CLG']);
        ItemCategory::where('name', 'Seragam Praktek')->update(['code' => 'CLC']);
        ItemCategory::where('name', 'Scrub Suit')->update(['code' => 'SCB']);
        ItemCategory::where('name', 'Jas Lab')->update(['code' => 'JLB']);
        ItemCategory::where('name', 'Seragam Komunitas')->update(['code' => 'COM']);
        ItemCategory::where('name', 'Sepatu Kuliah')->update(['code' => 'SCL']);
        ItemCategory::where('name', 'Sepatu Praktek')->update(['code' => 'SPR']);
        ItemCategory::where('name', 'Scrub Shoes')->update(['code' => 'SSH']);
        ItemCategory::where('name', 'Lanyard & Holder')->update(['code' => 'LYD']);
        ItemCategory::where('name', 'KTM')->update(['code' => 'KTM']);
        ItemCategory::where('name', 'Name Tag')->update(['code' => 'NAM']);
        ItemCategory::where('name', 'Nursing Kit')->update(['code' => 'NRS']);
        ItemCategory::where('name', 'Midwifery Kit')->update(['code' => 'MID']);

        Schema::table('item_categories', function (Blueprint $table) {
            $table->string('code', 3)->unique()->nullable(false)->change();
            $table->dropColumn('description');
        });
    }

    public function down(): void
    {
        Schema::table('item_categories', function (Blueprint $table) {
            $table->dropColumn(['code', 'deleted_at']);
            $table->text('description')->nullable()->after('name');
        });
    }
};
