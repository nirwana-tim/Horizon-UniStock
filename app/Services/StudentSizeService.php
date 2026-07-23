<?php

namespace App\Services;

use App\Models\Entitlement;
use App\Models\Item;
use App\Models\SizeChangeEvent;
use App\Models\Student;
use App\Models\StudentSizeHistory;
use App\Models\StudentSizeItem;
use App\Models\StudentSizeProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentSizeService
{
    public function getEntitlementItems(Student $student): Collection
    {
        if (! $student->entitlement_code) {
            return collect();
        }

        $entitlement = Entitlement::where('code', $student->entitlement_code)
            ->where('is_active', true)
            ->where('student_level', $student->student_level)
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

    public function getEventsForStudent(Student $student): \Illuminate\Support\Collection
    {
        return SizeChangeEvent::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get()
            ->filter(fn ($event) => $event->isApplicableToStudent($student))
            ->values();
    }

    public function getActiveEventForStudent(Student $student): ?SizeChangeEvent
    {
        return $this->getEventsForStudent($student)->first();
    }

    public function saveSizes(Student $student, array $sizes, ?int $eventId = null): void
    {
        $event = $eventId
            ? SizeChangeEvent::find($eventId)
            : $this->getActiveEventForStudent($student);

        if (! $event) {
            throw new \RuntimeException('Tidak ada event pengisian ukuran yang aktif saat ini.');
        }

        DB::transaction(function () use ($student, $sizes, $event) {
            $profile = StudentSizeProfile::where('student_id', $student->id)
                ->lockForUpdate()
                ->first();

            if (! $profile) {
                $profile = StudentSizeProfile::create([
                    'student_id' => $student->id,
                    'is_filled' => false,
                ]);
            }

            foreach ($sizes as $itemId => $size) {
                if (empty($size)) {
                    continue;
                }

                $sizeItem = StudentSizeItem::where('size_profile_id', $profile->id)
                    ->where('item_id', $itemId)
                    ->first();

                if ($sizeItem) {
                    if (! $event->canEdit($sizeItem)) {
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
        });
    }
}
