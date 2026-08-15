<?php

namespace App\Services;

use App\Models\DistributionItem;
use App\Models\DistributionSchedule;
use App\Models\DistributionTransaction;
use App\Models\EligibilityRecord;
use App\Models\Entitlement;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemPrice;
use App\Models\ItemVariant;
use App\Models\Student;
use App\Models\StudentSizeHistory;
use App\Models\StudentSizeItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DistributionService
{
    public function __construct(
        private readonly StockService $stockService,
        private readonly StudentSizeService $studentSizeService,
    ) {}

    public function findStudent(string $query): ?Student
    {
        return Student::with(['studyProgram', 'generation'])
            ->where('nim', $query)
            ->first();
    }

    public function getStudentEligibility(Student $student): ?EligibilityRecord
    {
        return $student->eligibilityRecords()->latest()->first();
    }

    public function isStudentEligible(Student $student): bool
    {
        $eligibility = $this->getStudentEligibility($student);

        return $eligibility && $eligibility->is_eligible;
    }

    public function getEntitlementForStudent(Student $student): ?Entitlement
    {
        if (! $student->entitlement_code) {
            return null;
        }

        return Entitlement::where('code', $student->entitlement_code)
            ->where('is_active', true)
            ->where(function ($q) use ($student) {
                $q->where('student_level', $student->student_level)
                    ->orWhereNull('student_level');
            })
            ->with('items.item')
            ->first();
    }

    /**
     * Find the specific item (by base_code + size) for distribution.
     * Returns the item with matching base_code and variant size.
     */
    public function findItemByBaseCodeAndSize(string $baseCode, string $size): ?Item
    {
        return Item::where('base_code', $baseCode)
            ->whereHas('variants', fn ($q) => $q->where('size', $size))
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
        return DB::transaction(function () use ($student, $schedule, $staff, $items, $manualNote) {
            Student::whereKey($student->id)->lockForUpdate()->first();

            $eligibility = EligibilityRecord::where('student_id', $student->id)
                ->lockForUpdate()
                ->first();
            if (! $eligibility || ! $eligibility->is_eligible) {
                throw new \Exception('Mahasiswa ini belum memenuhi syarat distribusi. Status pembayaran belum lunas.');
            }

            if (! $schedule->is_active) {
                throw new \Exception('Jadwal distribusi sudah tidak aktif. Silakan hubungi admin.');
            }

            if ($schedule->date && $schedule->date->lt(today())) {
                throw new \Exception(
                    'Jadwal distribusi "'.$schedule->name.'" sudah berakhir pada '.$schedule->date->format('d M Y').'. Tidak dapat melakukan scan melewati tanggal jadwal.'
                );
            }

            $isApplicable = DistributionSchedule::whereKey($schedule->id)
                ->forStudent($student)
                ->exists();
            if (! $isApplicable) {
                throw new \Exception(
                    'Jadwal distribusi "'.$schedule->name.'" tidak sesuai dengan mahasiswa ini. '.
                    'Pastikan fakultas/prodi/angkatan sesuai.'
                );
            }

            $existingTransaction = DistributionTransaction::where('student_id', $student->id)
                ->where('schedule_id', $schedule->id)
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()
                ->exists();

            if ($existingTransaction) {
                throw new \Exception('Mahasiswa ini sudah melakukan pengambilan pada jadwal "'.$schedule->name.'". Tidak boleh mengambil ulang.');
            }
            $transaction = DistributionTransaction::create([
                'student_id' => $student->id,
                'schedule_id' => $schedule->id,
                'staff_id' => $staff->id,
                'status' => 'completed',
                'pickup_time' => now(),
                'notes' => $manualNote,
            ]);

            $entitlement = $this->getEntitlementForStudent($student);
            $entitlementMap = $entitlement ? $entitlement->items->keyBy('item_id') : collect();

            $distributedByItem = DistributionItem::query()
                ->join('distribution_transactions', 'distribution_items.transaction_id', '=', 'distribution_transactions.id')
                ->where('distribution_transactions.student_id', $student->id)
                ->where('distribution_transactions.status', '!=', 'cancelled')
                ->groupBy('distribution_items.item_id')
                ->selectRaw('distribution_items.item_id, SUM(distribution_items.quantity) as total')
                ->pluck('total', 'item_id');

            $allFullyStocked = true;
            $autoNotes = [];

            foreach ($items as $itemData) {
                $item = null;
                if (! empty($itemData['base_code'])) {
                    $item = $this->findItemByBaseCodeAndSize($itemData['base_code'], $itemData['actual_size']);
                }
                if (! $item) {
                    $item = Item::find($itemData['item_id'] ?? 0);
                }
                if (! $item) {
                    continue;
                }

                $entitlementItem = $entitlementMap->get($item->id);
                if ($entitlementItem) {
                    $requestedQty = (int) ($itemData['quantity'] ?? 1);
                    $alreadyDistributed = (int) ($distributedByItem[$item->id] ?? 0);
                    $remaining = (int) $entitlementItem->quantity - $alreadyDistributed;

                    if ($remaining <= 0) {
                        throw new \Exception(
                            "Hak barang {$item->name} untuk mahasiswa ini sudah terpenuhi."
                        );
                    }

                    if ($requestedQty > $remaining) {
                        throw new \Exception(
                            "Jumlah {$item->name} melebihi hak distribusi ({$requestedQty} diminta, sisa hak {$remaining})."
                        );
                    }
                }

                $variant = ItemVariant::where('item_id', $item->id)
                    ->where('size', $itemData['actual_size'])
                    ->first();

                $quantity = (int) ($itemData['quantity'] ?? 1);
                $hppAtDistribution = 0;
                $deductedQty = 0;

                if (! $variant) {
                    $allFullyStocked = false;
                    $autoNotes[] = "Stok {$item->name} (Ukuran {$itemData['actual_size']}) tidak tersedia";
                } else {
                    $balance = $this->stockService->getBalance($item->id, $variant->id);
                    $availableStock = $balance ? $balance->quantity : 0;
                    $deductedQty = min($quantity, $availableStock);

                    if ($availableStock < $quantity) {
                        $allFullyStocked = false;
                        $shortage = $quantity - $availableStock;
                        $autoNotes[] = "Stok {$item->name} (Ukuran {$variant->size}) habis/kurang (kurang {$shortage} pcs)";
                    }

                    if ($deductedQty > 0) {
                        $fifoResult = $this->stockService->deductStockFifo(
                            $item->id,
                            $variant->id,
                            $deductedQty,
                            DistributionTransaction::class,
                            $transaction->id,
                            "Distribusi ke {$student->nim}"
                        );
                        $hppAtDistribution = $fifoResult['blended_hpp'];
                    } else {
                        $hppAtDistribution = 0;
                    }
                }

                $effectiveQty = $variant ? $deductedQty : 0;

                $sellingPrice = $this->getDeferredPrice($student, $item->id)
                    ?? $this->getSellingPriceForPeriod($item->id, $schedule);

                DistributionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item->id,
                    'variant_id' => $variant?->id,
                    'expected_size' => $itemData['expected_size'] ?? $itemData['actual_size'],
                    'actual_size' => $itemData['actual_size'],
                    'quantity' => $effectiveQty,
                    'hpp' => $hppAtDistribution,
                    'unit_price' => $sellingPrice,
                    'selling_price_at_distribution' => $sellingPrice,
                ]);

                $oldSize = $itemData['old_size'] ?? $itemData['expected_size'] ?? null;
                if ($oldSize && $oldSize !== $itemData['actual_size'] && $oldSize !== '-') {
                    $this->logSizeChange($student, $item, $oldSize, $itemData['actual_size'], $staff);
                }
            }

            // Check if there are items in entitlement that were not checked (deferred)
            $checkedBaseCodes = [];
            $resolveIds = collect($items)
                ->filter(fn ($i) => empty($i['base_code']) && ! empty($i['item_id']))
                ->pluck('item_id');
            $resolvedItems = Item::whereIn('id', $resolveIds)->pluck('base_code', 'id');
            foreach ($items as $itemData) {
                if (! empty($itemData['base_code'])) {
                    $checkedBaseCodes[] = $itemData['base_code'];
                } elseif (! empty($itemData['item_id'])) {
                    $baseCode = $resolvedItems[$itemData['item_id']] ?? null;
                    if ($baseCode) {
                        $checkedBaseCodes[] = $baseCode;
                    }
                }
            }
            $entitlement = $this->getEntitlementForStudent($student);
            if ($entitlement) {
                $entitlement->load('items.item');
            }

            $studentSizesByBaseCode = [];
            $sizeProfile = $student->activeSizeProfile;
            if ($sizeProfile && $entitlement) {
                $catIds = $entitlement->items->pluck('item.category_id')->filter()->unique();
                $categories = ItemCategory::whereIn('id', $catIds)->pluck('code', 'id');

                foreach ($entitlement->items as $entitlementItem) {
                    $item = $entitlementItem->item;
                    if (! $item) {
                        continue;
                    }

                    $catCode = $categories[$item->category_id] ?? null;
                    $stored = $catCode === 'UNF' ? $sizeProfile->baju_size
                        : ($catCode === 'SHO' ? $sizeProfile->sepatu_size : null);

                    if (empty($stored)) {
                        continue;
                    }

                    $resolved = $this->studentSizeService->resolveSizeValue($item, $stored);
                    $label = $resolved['label'] ?? $stored;

                    $studentSizesByBaseCode[$entitlementItem->item_id] = $label;
                    if ($item->base_code) {
                        $studentSizesByBaseCode[$item->base_code] = $label;
                    }
                }
            }
            if ($entitlement) {
                foreach ($entitlement->items as $entitlementItem) {
                    $entBaseCode = $entitlementItem->item?->base_code;
                    if ($entBaseCode && in_array($entBaseCode, $checkedBaseCodes)) {
                        continue;
                    }
                    if (in_array($entitlementItem->item_id, array_column($items, 'item_id'))) {
                        continue;
                    }
                    $allFullyStocked = false;
                    $expectedSize = $studentSizesByBaseCode[$entitlementItem->item_id]
                        ?? $studentSizesByBaseCode[$entBaseCode ?? ''] ?? '-';
                    $autoNotes[] = "{$entitlementItem->item->name} (Ukuran {$expectedSize}) ditunda/belum diambil";
                }
            }

            if (! $allFullyStocked) {
                $finalNotes = $manualNote;
                if (! empty($autoNotes)) {
                    $autoNotesStr = 'Sistem: '.implode(' | ', $autoNotes);
                    $finalNotes = $finalNotes ? $finalNotes.' | '.$autoNotesStr : $autoNotesStr;
                }
                $transaction->update([
                    'status' => 'partial',
                    'notes' => $finalNotes,
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

            DB::afterCommit(function () use ($transaction) {
                app(NotificationService::class)->sendDistributionConfirmation($transaction);

                if ($transaction->status === 'partial') {
                    app(NotificationService::class)->sendShortageAlert($transaction);
                }
            });

            return $transaction->fresh(['items.item', 'student', 'schedule']);
        }, attempts: 5);
    }

    private function getSellingPriceForPeriod(int $itemId, DistributionSchedule $schedule): float
    {
        return ItemPrice::where('item_id', $itemId)
            ->where('effective_date', '<=', $schedule->date)
            ->latest('effective_date')
            ->first()?->selling_price
            ?? Item::find($itemId)?->selling_price
            ?? 0;
    }

    private function getDeferredPrice(Student $student, int $itemId): ?float
    {
        $partialPrice = DistributionItem::where('item_id', $itemId)
            ->whereHas('transaction', fn ($q) => $q
                ->where('student_id', $student->id)
                ->where('status', 'partial')
            )
            ->orderByDesc('distribution_items.id')
            ->value('selling_price_at_distribution');

        return $partialPrice > 0 ? $partialPrice : null;
    }

    private function logSizeChange(Student $student, Item $item, string $oldSize, string $newSize, User $staff): void
    {
        $student->loadMissing('activeSizeProfile.sizeItems');
        $sizeProfile = $student->activeSizeProfile;
        if (! $sizeProfile) {
            return;
        }

        $item->loadMissing('category');

        $resolved = $this->studentSizeService->resolveSizeValue($item, $newSize);
        $newCode = $resolved['code'] ?? $newSize;
        $newLabel = $resolved['label'] ?? $newSize;

        $sizeItem = StudentSizeItem::where('size_profile_id', $sizeProfile->id)
            ->where('item_id', $item->id)
            ->first();

        if (! $sizeItem) {
            $sizeItem = StudentSizeItem::create([
                'size_profile_id' => $sizeProfile->id,
                'item_id' => $item->id,
                'size' => $newCode,
                'change_count' => 1,
            ]);

            StudentSizeHistory::create([
                'size_item_id' => $sizeItem->id,
                'old_size' => $oldSize,
                'new_size' => $newCode,
                'changed_by' => $staff->id,
                'changed_at' => now(),
            ]);
        } else {
            StudentSizeHistory::create([
                'size_item_id' => $sizeItem->id,
                'old_size' => $oldSize,
                'new_size' => $newCode,
                'changed_by' => $staff->id,
                'changed_at' => now(),
            ]);

            $sizeItem->update([
                'size' => $newCode,
                'change_count' => $sizeItem->change_count + 1,
            ]);
        }

        $catCode = $item->category?->code;
        if ($catCode === 'UNF') {
            $sizeProfile->update(['baju_size' => $newLabel]);
        } elseif ($catCode === 'SHO') {
            $sizeProfile->update(['sepatu_size' => $newLabel]);
        }

        AuditService::log(
            'size.updated',
            StudentSizeItem::class,
            $sizeItem?->id ?? $sizeProfile->id,
            ['size' => $oldSize],
            ['size' => $newCode, 'changed_by' => $staff->id, 'item_id' => $item->id]
        );
    }
}
