<?php

namespace App\Services;

use App\Models\DistributionItem;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\Entitlement;
use App\Models\Item;
use App\Models\ItemVariant;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DistributionService
{
    public function findStudent(string $query): ?Student
    {
        return Student::with(['studyProgram', 'programLevel'])
            ->where('nim', $query)
            ->orWhere('qr_token', $query)
            ->first();
    }

    public function getEntitlementForStudent(Student $student, DistributionSchedule $schedule): ?Entitlement
    {
        $stage = $schedule->stage;

        return Entitlement::where('study_program_id', $student->study_program_id)
            ->where('program_level_id', $student->program_level_id)
            ->where('period_id', $stage->period_id)
            ->where('student_type', $student->student_type)
            ->with('items.item')
            ->first();
    }

    public function processDistribution(
        Student $student,
        DistributionSchedule $schedule,
        User $staff,
        array $items
    ): DistributionTransaction {
        $stage = $schedule->stage;

        $existingTransaction = DistributionTransaction::where('student_id', $student->id)
            ->where('schedule_id', $schedule->id)
            ->exists();

        if ($existingTransaction) {
            throw new \Exception('Transaksi distribusi untuk mahasiswa ini pada jadwal ini sudah ada.');
        }

        return DB::transaction(function () use ($student, $schedule, $staff, $items, $stage) {
            $transaction = DistributionTransaction::create([
                'student_id' => $student->id,
                'schedule_id' => $schedule->id,
                'stage_id' => $stage->id,
                'staff_id' => $staff->id,
                'status' => 'completed',
                'pickup_time' => now(),
            ]);

            $allFullyStocked = true;

            foreach ($items as $itemData) {
                $item = Item::find($itemData['item_id']);
                if (!$item) {
                    continue;
                }

                $variant = ItemVariant::where('item_id', $item->id)
                    ->where('size', $itemData['actual_size'])
                    ->first();

                $quantity = (int) ($itemData['quantity'] ?? 1);

                if ($variant) {
                    $stockBalance = StockBalance::where('item_id', $item->id)
                        ->where('variant_id', $variant->id)
                        ->first();

                    $availableStock = $stockBalance ? $stockBalance->quantity - $stockBalance->reserved : 0;

                    if ($availableStock < $quantity) {
                        $allFullyStocked = false;
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

                if (isset($itemData['old_size']) && $itemData['old_size'] !== $itemData['actual_size']) {
                    $this->logSizeChange($student, $item, $itemData['old_size'], $itemData['actual_size'], $staff);
                }
            }

            if (!$allFullyStocked) {
                $transaction->update(['status' => 'partial']);
            }

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
            ->where('item_id', $item->id)
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
        }
    }
}
