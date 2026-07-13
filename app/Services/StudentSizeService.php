<?php

namespace App\Services;

use App\Models\Entitlement;
use App\Models\Item;
use App\Models\Student;
use App\Models\StudentSizeHistory;
use App\Models\StudentSizeItem;
use App\Models\StudentSizeProfile;
use Illuminate\Support\Collection;

class StudentSizeService
{
    public function getEntitlementItems(Student $student): Collection
    {
        if (! $student->entitlement_code) {
            return collect();
        }

        $entitlement = Entitlement::where('code', '=', $student->entitlement_code, 'and')
            ->where('is_active', '=', true, 'and')
            ->where('student_type', '=', $student->student_type, 'and')
            ->with(['items.item'])
            ->first();

        if (! $entitlement) {
            return collect();
        }

        $items = $entitlement->items
            ->pluck('item')
            ->filter(fn ($i) => $i && $i->base_code);

        if ($items->isEmpty()) {
            return collect();
        }

        $unique = $items->keyBy('base_code');

        $groupItems = Item::whereIn('base_code', $unique->keys())
            ->with('variants')
            ->get()
            ->groupBy('base_code');

        $unique->each(function ($item) use ($groupItems) {
            $group = $groupItems->get($item->base_code, collect());
            $allVariants = $group->flatMap->variants->unique('size')->values();
            $item->setRelation('variants', $allVariants);
        });

        return $unique->values();
    }

    public function saveSizes(Student $student, array $sizes): void
    {
        $profile = StudentSizeProfile::firstOrCreate(
            [
                'student_id' => $student->id,
            ],
            [
                'is_filled' => false,
            ]
        );

        foreach ($sizes as $itemId => $size) {
            if (empty($size)) {
                continue;
            }

            $sizeItem = StudentSizeItem::where('size_profile_id', '=', $profile->id, 'and')
                ->where('item_id', '=', $itemId, 'and')
                ->first();

            if ($sizeItem) {
                if ($sizeItem->change_count >= 1) {
                    continue;
                }

                if ($sizeItem->size !== $size) {
                    StudentSizeHistory::create([
                        'size_item_id' => $sizeItem->id,
                        'old_size' => $sizeItem->size,
                        'new_size' => $size,
                        'changed_by' => $student->user_id,
                        'changed_at' => now(),
                    ]);

                    $sizeItem->update([
                        'size' => $size,
                        'change_count' => $sizeItem->change_count + 1,
                    ]);
                }
            } else {
                StudentSizeItem::create([
                    'size_profile_id' => $profile->id,
                    'item_id' => $itemId,
                    'size' => $size,
                    'change_count' => 0,
                ]);
            }
        }

        $profile->update([
            'is_filled' => true,
            'filled_at' => $profile->filled_at ?? now(),
        ]);
    }
}
