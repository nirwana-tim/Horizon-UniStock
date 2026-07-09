<?php

namespace App\Services;

use App\Models\DistributionItem;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\EligibilityRecord;
use App\Models\Entitlement;
use App\Models\Item;
use App\Models\ItemVariant;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

class DistributionService
{
    public function findStudent(string $query): ?Student
    {
        return Student::with(['studyProgram', 'programLevel'])
            ->where('nim', $query)
            ->first();
    }

    public function getStudentEligibility(Student $student): ?EligibilityRecord
    {
        return $student->eligibilityRecords()->first();
    }

    public function isStudentEligible(Student $student): bool
    {
        $eligibility = $this->getStudentEligibility($student);

        return $eligibility && $eligibility->is_eligible;
    }

    public function getEntitlementForStudent(Student $student, DistributionSchedule $schedule): ?Entitlement
    {
        if (!$student->entitlement_code) {
            return null;
        }

        return Entitlement::where('code', '=', $student->entitlement_code, 'and')
            ->where('is_active', '=', true, 'and')
            ->with('items.item')
            ->first();
    }

    /**
     * Find the specific item (by base_code + size) for distribution.
     * Returns the item with matching base_code and variant size.
     */
    public function findItemByBaseCodeAndSize(string $baseCode, string $size): ?Item
    {
        return Item::where('base_code', '=', $baseCode, 'and')
            ->whereHas('variants', fn($q) => $q->where('size', '=', $size, 'and'))
            ->with('variants')
            ->first();
    }

    public function processDistribution(
        Student $student,
        DistributionSchedule $schedule,
        User $staff,
        array $items,
        ?string $manualNote = null
    ): DistributionTransaction {
        if (!$this->isStudentEligible($student)) {
            throw new \Exception('Mahasiswa ini belum memenuhi syarat distribusi. Status pembayaran belum lunas.');
        }

        $existingTransaction = DistributionTransaction::where('student_id', '=', $student->id, 'and')
            ->where('schedule_id', '=', $schedule->id, 'and')
            ->exists();

        if ($existingTransaction) {
            throw new \Exception('Transaksi distribusi untuk mahasiswa ini pada jadwal ini sudah ada.');
        }

        return DB::transaction(function () use ($student, $schedule, $staff, $items, $manualNote) {
            $transaction = DistributionTransaction::create([
                'student_id' => $student->id,
                'schedule_id' => $schedule->id,
                'staff_id' => $staff->id,
                'status' => 'completed',
                'pickup_time' => now(),
                'notes' => $manualNote,
            ]);

            $allFullyStocked = true;
            $autoNotes = [];

            foreach ($items as $itemData) {
                $item = Item::find($itemData['item_id'], ['*']);
                if (!$item) {
                    continue;
                }

                $variant = ItemVariant::where('item_id', '=', $item->id, 'and')
                    ->where('size', '=', $itemData['actual_size'], 'and')
                    ->first();

                $quantity = (int) ($itemData['quantity'] ?? 1);

                if ($variant) {
                    $stockBalance = StockBalance::where('item_id', '=', $item->id, 'and')
                        ->where('variant_id', '=', $variant->id, 'and')
                        ->lockForUpdate()
                        ->first();

                    $availableStock = $stockBalance ? $stockBalance->quantity - $stockBalance->reserved : 0;

                    if ($availableStock < $quantity) {
                        // Not enough stock — deduct whatever is available
                        $allFullyStocked = false;
                        $shortage = $quantity - $availableStock;
                        $autoNotes[] = "Stok {$item->name} (Ukuran {$variant->size}) habis/kurang (kurang {$shortage} pcs)";
                    }

                    $deductedQty = min($quantity, $availableStock);

                    if ($deductedQty > 0) {
                        StockMovement::create([
                            'item_id' => $item->id,
                            'variant_id' => $variant->id,
                            'type' => 'OUT',
                            'quantity' => $deductedQty,
                            'reference_type' => DistributionTransaction::class,
                            'reference_id' => $transaction->id,
                            'notes' => "Distribusi ke {$student->nim}",
                        ]);

                        if ($stockBalance) {
                            $stockBalance->decrement('quantity', $deductedQty);
                        }
                    }
                }

                DistributionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'expected_size' => $itemData['expected_size'] ?? $itemData['actual_size'],
                    'actual_size' => $itemData['actual_size'],
                    'quantity' => $quantity,
                ]);

                $oldSize = $itemData['old_size'] ?? $itemData['expected_size'] ?? null;
                if ($oldSize && $oldSize !== $itemData['actual_size'] && $oldSize !== '-') {
                    $this->logSizeChange($student, $item, $oldSize, $itemData['actual_size'], $staff);
                }
            }

            // Check if there are items in entitlement that were not checked (deferred)
            $checkedItemIds = array_column($items, 'item_id');
            $entitlement = $this->getEntitlementForStudent($student, $schedule);
            if ($entitlement) {
                $studentSizes = [];
                $sizeProfile = $student->activeSizeProfile;
                if ($sizeProfile) {
                    foreach ($sizeProfile->sizeItems as $sizeItem) {
                        $studentSizes[$sizeItem->item_id] = $sizeItem->size;
                    }
                }
                
                foreach ($entitlement->items as $entitlementItem) {
                    if (!in_array($entitlementItem->item_id, $checkedItemIds)) {
                        $allFullyStocked = false;
                        $expectedSize = $studentSizes[$entitlementItem->item_id] ?? '-';
                        $autoNotes[] = "{$entitlementItem->item->name} (Ukuran {$expectedSize}) ditunda/belum diambil";
                    }
                }
            }

            if (!$allFullyStocked) {
                $finalNotes = $manualNote;
                if (!empty($autoNotes)) {
                    $autoNotesStr = 'Sistem: ' . implode(' | ', $autoNotes);
                    $finalNotes = $finalNotes ? $finalNotes . ' | ' . $autoNotesStr : $autoNotesStr;
                }
                $transaction->update([
                    'status' => 'partial',
                    'notes' => $finalNotes
                ]);
            }

            AuditService::log(
                'distribution.created',
                DistributionTransaction::class,
                $transaction->id,
                null,
                [
                    'student_id' => $student->id,
                    'schedule_id' => $schedule->id,
                    'staff_id' => $staff->id,
                    'status' => $transaction->status,
                    'item_count' => count($items),
                ]
            );

            return $transaction->fresh(['items.item', 'student', 'schedule']);
        });
    }

    private function logSizeChange(Student $student, Item $item, string $oldSize, string $newSize, User $staff): void
    {
        $sizeProfile = $student->activeSizeProfile;
        if (!$sizeProfile) {
            return;
        }

        $sizeItem = $sizeProfile->sizeItems()
            ->where('item_id', '=', $item->id, 'and')
            ->first();

        if ($sizeItem) {
            \App\Models\StudentSizeHistory::create([
                'size_item_id' => $sizeItem->id,
                'old_size' => $oldSize,
                'new_size' => $newSize,
                'changed_by' => $staff->id,
                'changed_at' => now(),
            ]);

            $sizeItem->update(['size' => $newSize]);

            AuditService::log(
                'size.updated',
                \App\Models\StudentSizeItem::class,
                $sizeItem->id,
                ['size' => $oldSize],
                ['size' => $newSize, 'changed_by' => $staff->id, 'item_id' => $item->id]
            );
        }
    }
}
