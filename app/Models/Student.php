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
        'generation_id',
        'student_level',
        'status',
        'current_semester',
        'entitlement_code',
        'email_verified_at',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'leave' => 'Cuti',
            'graduated' => 'Lulus',
            'non_active' => 'Non-Aktif',
            default => ucfirst((string) ($this->status ?? 'active')),
        };
    }

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',
        ];
    }

    public function getStudentLevelLabelAttribute(): string
    {
        return $this->studentLevel?->deskripsi ?? $this->student_level ?? '-';
    }

    public function getCurrentSemesterLabelAttribute(): string
    {
        if (!$this->current_semester) return '-';
        $sem = strtoupper($this->current_semester);
        return 'Year ' . substr($sem, 1, 1) . ' Sem ' . substr($sem, 2, 1);
    }

    public static function generateEntitlementCode(Model $student): ?string
    {
        if (!$student->student_level || !$student->studyProgram?->faculty) {
            return null;
        }

        return $student->student_level
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

    public function generation(): BelongsTo
    {
        return $this->belongsTo(StudentGeneration::class, 'generation_id');
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

    public function studentLevel(): BelongsTo
    {
        return $this->belongsTo(StudentLevel::class, 'student_level', 'kode');
    }

    public function programLevel(): BelongsTo
    {
        return $this->belongsTo(StudentGeneration::class, 'generation_id');
    }
}
