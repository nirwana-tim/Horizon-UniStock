<?php

namespace App\Services;

use App\Models\DistributionSchedule;
use App\Models\Entitlement;
use App\Models\EntitlementItem;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class EntitlementService
{
    public function getEntitlement(Student $student, ?DistributionSchedule $schedule = null): ?Entitlement
    {
        $targetLevel = $schedule?->student_level ?? $student->student_level;

        $facultyCode = $student->studyProgram?->faculty?->code ?? '';
        $prodiCode = $student->studyProgram?->code ?? '';
        $entitlementCode = $targetLevel.$facultyCode.$prodiCode;

        return Entitlement::where('code', $entitlementCode)
            ->where('is_active', true)
            ->where(function ($q) use ($targetLevel) {
                $q->where('student_level', $targetLevel)
                    ->orWhereNull('student_level');
            })
            ->with('items.item')
            ->first();
    }

    public function validateEligibility(Student $student): bool
    {
        $eligibility = $student->eligibilityRecords()->first();

        return $eligibility && $eligibility->is_eligible;
    }

    public function createEntitlement(array $data): Entitlement
    {
        return DB::transaction(function () use ($data) {
            $entitlement = Entitlement::create([
                'code' => $data['code'],
                'student_level' => $data['student_level'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    EntitlementItem::updateOrCreate(
                        [
                            'entitlement_id' => $entitlement->id,
                            'item_id' => $itemData['item_id'],
                        ],
                        ['quantity' => $itemData['quantity'] ?? 1]
                    );
                }
            }

            return $entitlement->fresh(['items.item']);
        });
    }

    public function updateEntitlement(Entitlement $entitlement, array $data): Entitlement
    {
        return DB::transaction(function () use ($entitlement, $data) {
            $entitlement->update([
                'code' => $data['code'] ?? $entitlement->code,
                'student_level' => $data['student_level'] ?? $entitlement->student_level,
                'description' => $data['description'] ?? $entitlement->description,
                'is_active' => $data['is_active'] ?? $entitlement->is_active,
            ]);

            if (isset($data['items']) && is_array($data['items'])) {
                $entitlement->items()->delete();

                foreach ($data['items'] as $itemData) {
                    EntitlementItem::create([
                        'entitlement_id' => $entitlement->id,
                        'item_id' => $itemData['item_id'],
                        'quantity' => $itemData['quantity'] ?? 1,
                    ]);
                }
            }

            return $entitlement->fresh(['items.item']);
        });
    }

    public function deleteEntitlement(Entitlement $entitlement): void
    {
        DB::transaction(function () use ($entitlement) {
            $entitlement->items()->delete();
            $entitlement->delete();
        });
    }
}
