<?php

namespace App\Console\Commands;

use App\Models\ItemCategory;
use App\Models\StudentSizeItem;
use App\Models\StudentSizeProfile;
use Illuminate\Console\Command;

class SizesMigrateLegacyCommand extends Command
{
    protected $signature = 'sizes:migrate-legacy';

    protected $description = 'Migrate legacy per-item sizes to new baju_size/sepatu_size format';

    public function handle(): int
    {
        $bajuCategoryId = ItemCategory::where('code', 'UNF')->value('id');
        $sepatuCategoryId = ItemCategory::where('code', 'SHO')->value('id');

        if (! $bajuCategoryId && ! $sepatuCategoryId) {
            $this->error('Item categories UNF and SHO not found. Cannot migrate.');

            return Command::FAILURE;
        }

        $profiles = StudentSizeProfile::with(['student', 'sizeItems.item.category'])->get();
        $migrated = 0;
        $skipped = 0;

        foreach ($profiles as $profile) {
            if (! $profile->sizeItems->count()) {
                $skipped++;
                continue;
            }

            $bajuSize = null;
            $sepatuSize = null;

            foreach ($profile->sizeItems as $sizeItem) {
                $item = $sizeItem->item;
                if (! $item || ! $item->category_id) {
                    continue;
                }

                if ($item->category_id === $bajuCategoryId && ! $bajuSize) {
                    $bajuSize = $sizeItem->size;
                }

                if ($item->category_id === $sepatuCategoryId && ! $sepatuSize) {
                    $sepatuSize = $sizeItem->size;
                }
            }

            if ($bajuSize || $sepatuSize) {
                $profile->update([
                    'baju_size' => $profile->baju_size ?? $bajuSize,
                    'sepatu_size' => $profile->sepatu_size ?? $sepatuSize,
                ]);
                $migrated++;
                $studentLabel = $profile->student?->name ?: ('ID:' . $profile->student_id);
                $this->info("Migrated: {$studentLabel} — Baju: {$bajuSize}, Sepatu: {$sepatuSize}");
            } else {
                $skipped++;
            }
        }

        $this->info("Migration complete: {$migrated} migrated, {$skipped} skipped.");

        return Command::SUCCESS;
    }
}
