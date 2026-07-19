<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DistributionSchedule extends Model
{
    protected $fillable = [
        'name',
        'period',
        'semester',
        'stage_id',
        'student_type',
        'date',
        'location',
        'session',
        'is_active',
        'program_level_id',
        'faculty_id',
        'study_program_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(DistributionStage::class, 'stage_id');
    }

    public function scopeForStudent(Builder $query, Student $student): Builder
    {
        return $query
            ->where(fn (Builder $q) => $q->whereNull('student_type')->orWhere('student_type', $student->student_type))
            ->where(fn (Builder $q) => $q->whereNull('program_level_id')->orWhere('program_level_id', $student->program_level_id))
            ->where(fn (Builder $q) => $q->whereNull('faculty_id')->orWhere('faculty_id', $student->studyProgram?->faculty_id))
            ->where(fn (Builder $q) => $q->whereNull('study_program_id')->orWhere('study_program_id', $student->study_program_id));
    }

    public function getStudentTypeLabelAttribute(): string
    {
        return match ($this->student_type) {
            'year_1_sem_1' => 'Year 1 Sem 1',
            'year_1_sem_2' => 'Year 1 Sem 2',
            'year_2_sem_3' => 'Year 2 Sem 3',
            'year_2_sem_4' => 'Year 2 Sem 4',
            'continuing' => 'Continuing',
            default => $this->student_type ? ucfirst(str_replace('_', ' ', $this->student_type)) : 'All',
        };
    }

    public function programLevel(): BelongsTo
    {
        return $this->belongsTo(ProgramLevel::class, 'program_level_id');
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
}
