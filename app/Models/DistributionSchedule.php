<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DistributionSchedule extends Model
{
    protected $fillable = [
        'name',
        'period',
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
