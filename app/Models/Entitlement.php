<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entitlement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'study_program_id',
        'program_level_id',
        'student_type',
        'semester',
        'description',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'study_program_id');
    }

    public function programLevel(): BelongsTo
    {
        return $this->belongsTo(ProgramLevel::class, 'program_level_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(EntitlementItem::class, 'entitlement_id');
    }
}
