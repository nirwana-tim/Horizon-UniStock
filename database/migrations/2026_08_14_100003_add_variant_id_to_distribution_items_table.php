<?php

use App\Models\DistributionItem;
use App\Models\ItemVariant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_items', function (Blueprint $table) {
            $table->foreignId('variant_id')->nullable()->after('item_id');
        });

        DistributionItem::query()
            ->whereNull('variant_id')
            ->whereNotNull('actual_size')
            ->with('item')
            ->orderBy('id')
            ->chunkById(500, function ($items) {
                foreach ($items as $item) {
                    if (! $item->item) {
                        continue;
                    }

                    $variant = ItemVariant::where('item_id', $item->item_id)
                        ->where(function ($q) use ($item) {
                            $q->where('size', $item->actual_size)
                                ->orWhere('size_label', $item->actual_size);
                        })
                        ->first();

                    if ($variant) {
                        $item->update(['variant_id' => $variant->id]);
                    }
                }
            });

        Schema::table('distribution_items', function (Blueprint $table) {
            $table->foreign('variant_id', 'fk_distribution_items_variant_id')
                ->references('id')->on('item_variants')->nullOnDelete();
            $table->index(['item_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::table('distribution_items', function (Blueprint $table) {
            $table->dropForeign('fk_distribution_items_variant_id');
            $table->dropIndex(['item_id', 'variant_id']);
            $table->dropColumn('variant_id');
        });
    }
};