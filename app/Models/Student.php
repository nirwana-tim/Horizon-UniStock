<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    protected $fillable = [
        'user_id',
        'nim',
        'name',
        'email_kampus',
        'email_pribadi',

        'study_program_id',
        'program_level_id',
        'student_type',
        'current_semester',
        'entitlement_code',
        'email_verified_at',
    ];

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',
        ];
    }

    public function getStudentTypeLabelAttribute(): string
    {
        return match ($this->student_type) {
            'year_1_sem_1' => 'Year 1 Sem 1',
            'year_1_sem_2' => 'Year 1 Sem 2',
            'year_2_sem_3' => 'Year 2 Sem 3',
            'year_2_sem_4' => 'Year 2 Sem 4',
            'continuing' => 'Continuing',
            default => ucfirst(str_replace('_', ' ', $this->student_type)),
        };
    }

    public function getCurrentSemesterLabelAttribute(): string
    {
        if (!$this->current_semester) return '-';
        $sem = strtoupper($this->current_semester);
        return 'Year ' . substr($sem, 1, 1) . ' Sem ' . substr($sem, 2, 1);
    }

    public static function generateEntitlementCode(Model $student): ?string
    {
        if (!$student->programLevel || !$student->studyProgram?->faculty) {
            return null;
        }

        return $student->programLevel->code
            . $student->studyProgram->faculty->code
            . $student->studyProgram->code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function programLevel(): BelongsTo
    {
        return $this->belongsTo(ProgramLevel::class);
    }

    public function eligibilityRecords(): HasMany
    {
        return $this->hasMany(EligibilityRecord::class);
    }

    public function sizeProfiles(): HasMany
    {
        return $this->hasMany(StudentSizeProfile::class);
    }

    public function distributionTransactions(): HasMany
    {
        return $this->hasMany(DistributionTransaction::class);
    }

    public function emailNotifications(): HasMany
    {
        return $this->hasMany(EmailNotification::class);
    }

    public function activeSizeProfile(): HasOne
    {
        return $this->hasOne(StudentSizeProfile::class)->where('is_filled', true)->latest();
    }
}
