<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class DistributionSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'period',
        'student_level',
        'date',
        'location',
        'session',
        'start_time',
        'end_time',
        'is_active',
        'faculty_id',
        'study_program_id',
        'generation_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'is_active' => 'boolean',
        ];
    }

    public function scopeForStudent(Builder $query, Student $student): Builder
    {
        return $query
            ->where(fn (Builder $q) => $q->whereNull('student_level')->orWhere('student_level', $student->student_level))
            ->where(fn (Builder $q) => $q->whereNull('faculty_id')->orWhere('faculty_id', $student->studyProgram?->faculty_id))
            ->where(fn (Builder $q) => $q->whereNull('study_program_id')->orWhere('study_program_id', $student->study_program_id))
            ->where(fn (Builder $q) => $q->whereNull('generation_id')->orWhere('generation_id', $student->generation_id));
    }

    public function scopeUpcomingForStudent(Builder $query, Student $student): Builder
    {
        return $query
            ->with(['faculty', 'generation'])
            ->where('is_active', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->forStudent($student)
            ->orderBy('date');
    }

    public function scopeActiveNow(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query
            ->where('is_active', true)
            ->where('date', $now->toDateString())
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('start_time')
                    ->orWhere('start_time', '<=', $now->format('H:i:s'));
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('end_time')
                    ->orWhere('end_time', '>=', $now->format('H:i:s'));
            });
    }

    public function isActiveNow(): bool
    {
        if (! $this->is_active || ! $this->date) {
            return false;
        }

        $now = Carbon::now();

        if ($this->date->toDateString() !== $now->toDateString()) {
            return false;
        }

        if ($this->start_time && $now->format('H:i:s') < $this->start_time->format('H:i:s')) {
            return false;
        }

        if ($this->end_time && $now->format('H:i:s') > $this->end_time->format('H:i:s')) {
            return false;
        }

        return true;
    }

    public function isExpired(): bool
    {
        if (! $this->date) {
            return false;
        }

        $now = Carbon::now();

        if ($this->date->lt($now->startOfDay())) {
            return true;
        }

        if ($this->date->eq($now->startOfDay()) && $this->end_time && $now->format('H:i:s') > $this->end_time->format('H:i:s')) {
            return true;
        }

        return false;
    }

    public function timeWindowLabel(): string
    {
        if (! $this->start_time || ! $this->end_time) {
            return $this->session ?? '-';
        }

        return $this->start_time->format('H:i').' - '.$this->end_time->format('H:i');
    }

    public function getStudentLevelLabelAttribute(): string
    {
        if (! $this->student_level) {
            return 'All';
        }

        return $this->studentLevel?->deskripsi ?? $this->student_level;
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(StudentGeneration::class, 'generation_id');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'study_program_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DistScheduleItem::class, 'schedule_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(DistributionTransaction::class, 'schedule_id');
    }

    public function emailNotifications(): HasMany
    {
        return $this->hasMany(EmailNotification::class, 'schedule_id');
    }

    public function studentLevel(): BelongsTo
    {
        return $this->belongsTo(StudentLevel::class, 'student_level', 'kode');
    }
}
