<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ItemDepartment extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'label'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'department_id');
    }

    public function studyPrograms(): BelongsToMany
    {
        return $this->belongsToMany(StudyProgram::class, 'department_study_program', 'department_id', 'study_program_id');
    }
}
