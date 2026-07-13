<?php

namespace App\Services;

use App\Models\Entitlement;
use App\Models\EntitlementItem;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class EntitlementService
{
    public function getEntitlement(Student $student): ?Entitlement
    {
        if (! $student->entitlement_code) {
            return null;
        }

        return Entitlement::where('code', $student->entitlement_code)
            ->where('is_active', true)
            ->where('student_type', $student->student_type)
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
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    EntitlementItem::create([
                        'entitlement_id' => $entitlement->id,
                        'item_id' => $itemData['item_id'],
                        'quantity' => $itemData['quantity'] ?? 1,
                    ]);
                }
            }

            AuditService::log(
                'entitlement.created',
                Entitlement::class,
                $entitlement->id,
                null,
                $entitlement->toArray()
            );

            return $entitlement->fresh(['items.item']);
        });
    }

    public function updateEntitlement(Entitlement $entitlement, array $data): Entitlement
    {
        return DB::transaction(function () use ($entitlement, $data) {
            $oldValues = $entitlement->toArray();

            $entitlement->update([
                'code' => $data['code'] ?? $entitlement->code,
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

            AuditService::log(
                'entitlement.updated',
                Entitlement::class,
                $entitlement->id,
                $oldValues,
                $entitlement->fresh()->toArray()
            );

            return $entitlement->fresh(['items.item']);
        });
    }

    public function deleteEntitlement(Entitlement $entitlement): void
    {
        DB::transaction(function () use ($entitlement) {
            AuditService::log(
                'entitlement.deleted',
                Entitlement::class,
                $entitlement->id,
                $entitlement->toArray(),
                null
            );

            $entitlement->items()->delete();
            $entitlement->delete();
        });
    }
}
