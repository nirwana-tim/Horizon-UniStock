<?php

namespace App\Services;

use App\Models\Entitlement;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemSize;
use App\Models\SizeChangeEvent;
use App\Models\SizeEventSubmission;
use App\Models\Student;
use App\Models\StudentSizeHistory;
use App\Models\StudentSizeItem;
use App\Models\StudentSizeProfile;
use App\Services\AuditService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentSizeService
{
    /**
     * Default size options (fallback when event has no custom options).
     * These are derived from DB seeders but cached here for performance.
     */
    private const DEFAULT_BAJU_SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', 'XXXXL', 'XXXXXL', 'XXXXXXL'];
    private const DEFAULT_SEPATU_SIZES = ['38', '39', '40', '41', '42', '43', '44', '45'];

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

    /**
     * Get size options for baju and sepatu.
     * Merges event-specific JSON overrides with DB defaults.
     */
    public function getSizeOptions(SizeChangeEvent $event): array
    {
        $bajuOptions = $event->baju_size_options ?? $this->getDefaultBajuSizes();
        $sepatuOptions = $event->sepatu_size_options ?? $this->getDefaultSepatuSizes();

        return [
            'baju' => $bajuOptions,
            'sepatu' => $sepatuOptions,
        ];
    }

    /**
     * Get default baju sizes from DB (category UNF) with fallback to hardcoded.
     */
    public function getDefaultBajuSizes(): array
    {
        try {
            $unfCategory = ItemCategory::where('code', 'UNF')->first();
            if (! $unfCategory) {
                return self::DEFAULT_BAJU_SIZES;
            }

            $sizes = ItemSize::whereHas('categories', fn ($q) => $q->where('item_category_id', $unfCategory->id))
                ->orderBy('code')
                ->pluck('label')
                ->filter()
                ->values()
                ->toArray();

            return $sizes->isNotEmpty() ? $sizes->toArray() : self::DEFAULT_BAJU_SIZES;
        } catch (\Exception $e) {
            return self::DEFAULT_BAJU_SIZES;
        }
    }

    /**
     * Get default sepatu sizes from DB (category SHO) with fallback to hardcoded.
     */
    public function getDefaultSepatuSizes(): array
    {
        try {
            $shoCategory = ItemCategory::where('code', 'SHO')->first();
            if (! $shoCategory) {
                return self::DEFAULT_SEPATU_SIZES;
            }

            $sizes = ItemSize::whereHas('categories', fn ($q) => $q->where('item_category_id', $shoCategory->id))
                ->orderBy('code')
                ->pluck('label')
                ->filter()
                ->values()
                ->toArray();

            return $sizes->isNotEmpty() ? $sizes->toArray() : self::DEFAULT_SEPATU_SIZES;
        } catch (\Exception $e) {
            return self::DEFAULT_SEPATU_SIZES;
        }
    }

    /**
     * Save student sizes (baju + sepatu) for a given event.
     *
     * @param  Student  $student
     * @param  array{baju: string, sepatu: string}  $sizes
     * @param  int|null  $eventId
     */
    public function saveSizes(Student $student, array $sizes, ?int $eventId = null): void
    {
        $event = $eventId
            ? SizeChangeEvent::find($eventId)
            : $this->getActiveEventForStudent($student);

        if (! $event) {
            throw new \RuntimeException('Tidak ada event pengisian ukuran yang aktif saat ini.');
        }

        $baju = $sizes['baju'] ?? null;
        $sepatu = $sizes['sepatu'] ?? null;

        if (empty($baju) && empty($sepatu)) {
            throw new \RuntimeException('Pilih minimal satu ukuran (Baju atau Sepatu).');
        }

        DB::transaction(function () use ($student, $baju, $sepatu, $event) {
            $profile = StudentSizeProfile::where('student_id', $student->id)
                ->lockForUpdate()
                ->first();

            if (! $profile) {
                $profile = StudentSizeProfile::create([
                    'student_id' => $student->id,
                    'is_filled' => false,
                ]);
            }

            $submission = SizeEventSubmission::where('student_id', $student->id)
                ->where('event_id', $event->id)
                ->lockForUpdate()
                ->first();

            $submissionCount = $submission?->submission_count ?? 0;
            if ($submissionCount >= $event->max_changes) {
                throw new \RuntimeException(
                    'Kamu sudah mencapai batas maksimal pengisian (' . $event->max_changes . 'x) untuk event ini.'
                );
            }

            $oldBaju = $profile->baju_size;
            $oldSepatu = $profile->sepatu_size;

            if (! empty($baju)) {
                $profile->update(['baju_size' => $baju]);
            }

            if (! empty($sepatu)) {
                $profile->update(['sepatu_size' => $sepatu]);
            }

            if (! $submission) {
                $submission = SizeEventSubmission::create([
                    'student_id' => $student->id,
                    'event_id' => $event->id,
                    'submission_count' => 0,
                ]);
            }
            $submission->increment('submission_count');

            $profile->update([
                'is_filled' => true,
                'filled_at' => $profile->filled_at ?? now(),
            ]);

            AuditService::log('save_sizes', StudentSizeProfile::class, $profile->id, [
                'student_id' => $student->id,
                'event_id' => $event->id,
                'submission_count' => $submission->submission_count,
                'baju_size' => $baju,
                'sepatu_size' => $sepatu,
                'old_baju_size' => $oldBaju,
                'old_sepatu_size' => $oldSepatu,
            ]);
        });
    }
}
