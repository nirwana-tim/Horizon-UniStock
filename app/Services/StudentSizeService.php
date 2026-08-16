<?php

namespace App\Services;

use App\Models\Entitlement;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemSize;
use App\Models\ItemVariant;
use App\Models\SizeChangeEvent;
use App\Models\SizeEventSubmission;
use App\Models\Student;
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

    public function getEventsForStudent(Student $student): Collection
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
     * Hanya mengikuti opsi yang diisi admin pada event; tanpa fallback dari tabel lain.
     */
    public function getSizeOptions(SizeChangeEvent $event): array
    {
        return [
            'baju' => $event->baju_size_options ?? [],
            'sepatu' => $event->sepatu_size_options ?? [],
        ];
    }

    /**
     * Normalize a size value (label or code) to its canonical code + label for a category.
     *
     * @return array{code: string, label: string}|null
     */
    public function normalizeSize(?string $value, string $categoryCode): ?array
    {
        if (empty($value)) {
            return null;
        }

        $categoryId = ItemCategory::where('code', $categoryCode)->value('id');
        if (! $categoryId) {
            return ['code' => $value, 'label' => $value];
        }

        $pairs = ItemSize::whereHas('categories', fn ($q) => $q->where('item_category_id', $categoryId))
            ->get(['code', 'label'])
            ->map(fn ($size) => ['code' => $size->code, 'label' => $size->label]);

        if ($pairs->isNotEmpty()) {
            $found = $this->resolveFromPairs($pairs, $value);
            if ($found) {
                return $found;
            }
        }

        $pairs = ItemVariant::whereHas('item', fn ($q) => $q->where('category_id', $categoryId))
            ->get(['size', 'size_label'])
            ->map(fn ($variant) => ['code' => $variant->size, 'label' => $variant->size_label]);

        if ($pairs->isNotEmpty()) {
            $found = $this->resolveFromPairs($pairs, $value);
            if ($found) {
                return $found;
            }
        }

        return ['code' => $value, 'label' => $value];
    }

    /**
     * Resolve a size value (label or code) to its canonical code + label for a given item.
     *
     * @return array{code: string, label: string}|null
     */
    public function resolveSizeValue(Item $item, ?string $value): ?array
    {
        if (empty($value)) {
            return null;
        }

        $item->loadMissing('variants', 'category');

        $pairs = $item->variants
            ->map(fn ($variant) => ['code' => $variant->size, 'label' => $variant->size_label]);

        if ($pairs->isNotEmpty()) {
            $found = $this->resolveFromPairs($pairs, $value);
            if ($found) {
                return $found;
            }
        }

        $categoryCode = $item->category?->code;
        if ($categoryCode) {
            $normalized = $this->normalizeSize($value, $categoryCode);
            if ($normalized) {
                return $normalized;
            }
        }

        return ['code' => $value, 'label' => $value];
    }

    /**
     * Match a value (code or label) against a collection of code/label pairs.
     *
     * @param  Collection<int, array{code: string, label: string}>  $pairs
     * @return array{code: string, label: string}|null
     */
    private function resolveFromPairs(Collection $pairs, string $value): ?array
    {
        $byCode = $pairs->firstWhere('code', $value);
        if ($byCode) {
            return ['code' => $byCode['code'], 'label' => $byCode['label']];
        }

        $byLabel = $pairs->firstWhere('label', $value);
        if ($byLabel) {
            return ['code' => $byLabel['code'], 'label' => $byLabel['label']];
        }

        return null;
    }

    /**
     * Keep the legacy per-item table in sync for a generic size (baju/sepatu).
     */
    private function syncGenericSizeItems(StudentSizeProfile $profile, string $categoryCode, ?string $size): void
    {
        if (empty($size)) {
            return;
        }

        $normalized = $this->normalizeSize($size, $categoryCode);
        $code = $normalized['code'] ?? $size;

        $categoryId = ItemCategory::where('code', $categoryCode)->value('id');
        if (! $categoryId) {
            return;
        }

        $itemIds = Item::where('category_id', $categoryId)->pluck('id');

        foreach ($itemIds as $itemId) {
            $existing = StudentSizeItem::where('size_profile_id', $profile->id)
                ->where('item_id', $itemId)
                ->first();

            StudentSizeItem::updateOrCreate(
                ['size_profile_id' => $profile->id, 'item_id' => $itemId],
                ['size' => $code, 'change_count' => ($existing ? $existing->change_count : 0) + 1]
            );
        }
    }

    /**
     * Save student sizes (baju + sepatu) for a given event.
     *
     * @param  array{baju: string, sepatu: string}  $sizes
     */
    public function saveSizes(Student $student, array $sizes, ?int $eventId = null): void
    {
        $event = $eventId
            ? SizeChangeEvent::find($eventId)
            : $this->getActiveEventForStudent($student);

        if (! $event) {
            throw new \RuntimeException('Tidak ada event pengisian ukuran yang aktif saat ini.');
        }

        if (! $event->isApplicableToStudent($student)) {
            throw new \RuntimeException('Event ini tidak berlaku untuk kamu atau sudah tidak aktif.');
        }

        $baju = $sizes['baju'] ?? null;
        $sepatu = $sizes['sepatu'] ?? null;

        if (empty($baju) && empty($sepatu)) {
            throw new \RuntimeException('Pilih minimal satu ukuran (Baju atau Sepatu).');
        }

        $options = $this->getSizeOptions($event);

        if (! empty($baju) && ! in_array($baju, $options['baju'], true)) {
            throw new \RuntimeException('Ukuran Baju tidak tersedia pada event ini.');
        }

        if (! empty($sepatu) && ! in_array($sepatu, $options['sepatu'], true)) {
            throw new \RuntimeException('Ukuran Sepatu tidak tersedia pada event ini.');
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

            if (! $event->allow_reedit && $submissionCount > 0) {
                throw new \RuntimeException(
                    'Ukuran hanya boleh diisi sekali untuk event ini (re-edit tidak diizinkan).'
                );
            }

            if ($submissionCount >= $event->max_changes) {
                throw new \RuntimeException(
                    'Kamu sudah mencapai batas maksimal pengisian ('.$event->max_changes.'x) untuk event ini.'
                );
            }

            $oldBaju = $profile->baju_size;
            $oldSepatu = $profile->sepatu_size;

            if (! empty($baju)) {
                $profile->update(['baju_size' => $baju]);
                $this->syncGenericSizeItems($profile, 'UNF', $baju);
            }

            if (! empty($sepatu)) {
                $profile->update(['sepatu_size' => $sepatu]);
                $this->syncGenericSizeItems($profile, 'SHO', $sepatu);
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
