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
        'student_level',
        'date',
        'location',
        'session',
        'is_active',
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

    public function scopeForStudent(Builder $query, Student $student): Builder
    {
        return $query
            ->where(fn (Builder $q) => $q->whereNull('student_level')->orWhere('student_level', $student->student_level))
            ->where(fn (Builder $q) => $q->whereNull('faculty_id')->orWhere('faculty_id', $student->studyProgram?->faculty_id))
            ->where(fn (Builder $q) => $q->whereNull('study_program_id')->orWhere('study_program_id', $student->study_program_id));
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

    public function programLevel(): BelongsTo
    {
        return $this->belongsTo(StudentGeneration::class, 'generation_id');
    }
}
