<?php

namespace App\Services;

use App\Models\DistributionSchedule;
use App\Models\Entitlement;
use App\Models\Faculty;
use App\Models\Item;
use App\Models\Student;
use App\Models\StudyProgram;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DistributionScheduleService
{
    public function store(array $data): DistributionSchedule
    {
        return DB::transaction(function () use ($data) {
            $itemIds = $data['item_ids'] ?? [];
            unset($data['item_ids']);

            if (($data['study_program_id'] ?? null) === 'all') {
                $data['study_program_id'] = null;
            }

            $schedule = DistributionSchedule::create($data);

            foreach ($itemIds as $itemId) {
                $schedule->items()->create(['item_id' => $itemId]);
            }

            AuditService::log(
                'create',
                DistributionSchedule::class,
                $schedule->id,
                null,
                array_merge($schedule->fresh()->toArray(), ['item_ids' => $itemIds])
            );

            DB::afterCommit(function () use ($schedule) {
                app(NotificationService::class)->notifyScheduleCreated($schedule);
            });

            return $schedule;
        });
    }

    public function update(DistributionSchedule $schedule, array $data): DistributionSchedule
    {
        return DB::transaction(function () use ($schedule, $data) {
            $itemIds = $data['item_ids'] ?? [];
            unset($data['item_ids']);

            if (($data['study_program_id'] ?? null) === 'all') {
                $data['study_program_id'] = null;
            }

            $oldValues = array_merge($schedule->toArray(), [
                'item_ids' => $schedule->items()->pluck('item_id')->toArray(),
            ]);

            $schedule->update($data);

            $schedule->items()->delete();

            foreach ($itemIds as $itemId) {
                $schedule->items()->create(['item_id' => $itemId]);
            }

            AuditService::log(
                'update',
                DistributionSchedule::class,
                $schedule->id,
                $oldValues,
                array_merge($schedule->fresh()->toArray(), ['item_ids' => $itemIds])
            );

            return $schedule;
        });
    }

    public function destroy(DistributionSchedule $schedule): void
    {
        DB::transaction(function () use ($schedule) {
            $oldValues = array_merge($schedule->toArray(), [
                'item_ids' => $schedule->items()->pluck('item_id')->toArray(),
            ]);

            $schedule->items()->delete();
            $schedule->delete();

            AuditService::log('delete', DistributionSchedule::class, $schedule->id, $oldValues, null);
        });
    }

    public function fetchItems(?int $studyProgramId, ?int $facultyId, ?string $studentLevel, array $checkedIds = [], ?string $search = null): array
    {
        if ($studyProgramId === null || $studyProgramId === 0) {
            $items = collect();
        } elseif ($studyProgramId === -1) {
            $items = Item::when($search, function ($q, $search) {
                $search = str_replace(['%', '_'], ['\%', '\_'], $search);
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })->orderBy('name')->get();
        } else {
            $studyProgram = StudyProgram::with('faculty')->find($studyProgramId);

            $allowedIds = collect();

            if ($studyProgram) {
                $facultyCode = $facultyId
                    ? (Faculty::find($facultyId)?->code ?? '')
                    : ($studyProgram->faculty?->code ?? '');
                $prodiCode = $studyProgram->code;

                $codeLike = $studentLevel
                    ? $studentLevel.$facultyCode.$prodiCode.'%'
                    : '%'.$facultyCode.$prodiCode.'%';

                $entitlementCodes = Student::query()
                    ->where('study_program_id', $studyProgram->id)
                    ->whereNotNull('entitlement_code')
                    ->when($studentLevel, fn ($q) => $q->where('student_level', $studentLevel))
                    ->distinct()
                    ->pluck('entitlement_code');

                $entitlements = Entitlement::with('items')
                    ->where('is_active', true)
                    ->when($studentLevel, fn ($q) => $q->where('student_level', $studentLevel))
                    ->where(function ($q) use ($entitlementCodes, $codeLike) {
                        if ($entitlementCodes->isNotEmpty()) {
                            $q->whereIn('code', $entitlementCodes);
                        }
                        $q->orWhere('code', 'like', $codeLike);
                    })
                    ->get();

                $allowedIds = $entitlements->flatMap(fn ($e) => $e->items->pluck('item_id'))->unique()->values();
            }

            $items = $allowedIds->isNotEmpty()
                ? Item::whereIn('id', $allowedIds)
                    ->when($search, function ($q, $search) {
                        $search = str_replace(['%', '_'], ['\%', '\_'], $search);
                        $q->where(function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                    })
                    ->orderBy('name')->get()
                : collect();
        }

        return [$items, $checkedIds];
    }

    public function getFilteredSchedules(?string $search, ?string $period, ?int $facultyId, ?int $studyProgramId): LengthAwarePaginator
    {
        return DistributionSchedule::with('faculty', 'studyProgram', 'studentLevel')
            ->when($search, function ($query, $search) {
                $search = str_replace(['%', '_'], ['\%', '\_'], $search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($period, fn ($query, $p) => $query->where('period', $p))
            ->when($facultyId, fn ($query, $f) => $query->where('faculty_id', $f))
            ->when($studyProgramId, fn ($query, $s) => $query->where('study_program_id', $s))
            ->latest()
            ->paginate(20);
    }

    public function getFormOptions(): array
    {
        return [
            'faculties' => cache()->remember('faculties-all', 3600, fn () => Faculty::orderBy('name')->get()
            ),
            'studyPrograms' => cache()->remember('study-programs-faculty', 3600, fn () => StudyProgram::with('faculty')->orderBy('name')->get()
            ),
            'periods' => cache()->remember('schedule-periods', 3600, fn () => DistributionSchedule::whereNotNull('period')
                ->distinct()
                ->orderBy('period', 'desc')
                ->pluck('period')
                ->map(fn ($p) => (string) $p)
                ->values()
                ->toArray()
            ),
        ];
    }
}
