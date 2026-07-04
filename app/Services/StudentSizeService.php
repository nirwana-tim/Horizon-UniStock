<?php

namespace App\Services;

use App\Models\Entitlement;
use App\Models\Student;
use App\Models\StudentSizeHistory;
use App\Models\StudentSizeItem;
use App\Models\StudentSizeProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StudentSizeService
{
    public function getEntitlementItems(Student $student): Collection
    {
        $entitlement = Entitlement::where('study_program_id', $student->study_program_id)
            ->where('program_level_id', $student->program_level_id)
            ->where('student_type', $student->student_type)
            ->with('items.item.variants')
            ->first();

        if (!$entitlement) {
            return collect();
        }

        return $entitlement->items->pluck('item')->filter();
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

            $sizeItem = StudentSizeItem::where('size_profile_id', $profile->id)
                ->where('item_id', $itemId)
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

    public function generateQr(Student $student): string
    {
        if ($student->qr_token) {
            return $student->qr_token;
        }

        $token = (string) Str::uuid();

        $student->update([
            'qr_token' => $token,
            'qr_generated_at' => now(),
        ]);

        return $token;
    }
}
