<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\DistributionPeriod;
use App\Models\Entitlement;
use App\Models\EntitlementItem;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntitlementService
{
    public function getEntitlement(Student $student, DistributionPeriod $period): ?Entitlement
    {
        return Entitlement::where('study_program_id', $student->study_program_id)
            ->where('program_level_id', $student->program_level_id)
            ->where('period_id', $period->id)
            ->where('student_type', $student->student_type)
            ->with('items.item')
            ->first();
    }

    public function validateEligibility(Student $student, DistributionPeriod $period): bool
    {
        $eligibility = $student->eligibilityRecords()
            ->where('period_id', $period->id)
            ->first();

        return $eligibility && $eligibility->is_eligible;
    }

    public function getAllForPeriod(DistributionPeriod $period): Collection
    {
        return Entitlement::with(['studyProgram', 'programLevel', 'items.item'])
            ->where('period_id', $period->id)
            ->get();
    }

    public function createEntitlement(array $data): Entitlement
    {
        return DB::transaction(function () use ($data) {
            $entitlement = Entitlement::create([
                'study_program_id' => $data['study_program_id'],
                'program_level_id' => $data['program_level_id'],
                'period_id' => $data['period_id'],
                'student_type' => $data['student_type'],
                'description' => $data['description'] ?? null,
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

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'model_type' => Entitlement::class,
                'model_id' => $entitlement->id,
                'new_values' => $entitlement->toArray(),
                'ip_address' => request()->ip(),
            ]);

            return $entitlement->fresh(['items.item', 'studyProgram', 'programLevel']);
        });
    }

    public function updateEntitlement(Entitlement $entitlement, array $data): Entitlement
    {
        return DB::transaction(function () use ($entitlement, $data) {
            $oldValues = $entitlement->toArray();

            $entitlement->update([
                'study_program_id' => $data['study_program_id'] ?? $entitlement->study_program_id,
                'program_level_id' => $data['program_level_id'] ?? $entitlement->program_level_id,
                'student_type' => $data['student_type'] ?? $entitlement->student_type,
                'description' => $data['description'] ?? $entitlement->description,
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

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'model_type' => Entitlement::class,
                'model_id' => $entitlement->id,
                'old_values' => $oldValues,
                'new_values' => $entitlement->fresh()->toArray(),
                'ip_address' => request()->ip(),
            ]);

            return $entitlement->fresh(['items.item', 'studyProgram', 'programLevel']);
        });
    }

    public function deleteEntitlement(Entitlement $entitlement): void
    {
        DB::transaction(function () use ($entitlement) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'model_type' => Entitlement::class,
                'model_id' => $entitlement->id,
                'old_values' => $entitlement->toArray(),
                'ip_address' => request()->ip(),
            ]);

            $entitlement->items()->delete();
            $entitlement->delete();
        });
    }
}
