<?php

namespace App\Models;

use App\Models\StudentSizeItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SizeChangeEvent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'faculty_id',
        'study_program_id',
        'generation_id',
        'student_level',
        'max_changes',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_active' => 'boolean',
            'max_changes' => 'integer',
        ];
    }

    public function canEdit(StudentSizeItem $sizeItem): bool
    {
        return $sizeItem->change_count < $this->max_changes;
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(StudentGeneration::class, 'generation_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isApplicableToStudent(Student $student): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        if ($this->faculty_id && $student->studyProgram?->faculty_id !== $this->faculty_id) {
            return false;
        }

        if ($this->study_program_id && $student->study_program_id !== $this->study_program_id) {
            return false;
        }

        if ($this->generation_id && $student->generation_id !== $this->generation_id) {
            return false;
        }

        if ($this->student_level && strtolower((string)$student->student_level) !== strtolower((string)$this->student_level)) {
            return false;
        }

        return true;
    }
}
