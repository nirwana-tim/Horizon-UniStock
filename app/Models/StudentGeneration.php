<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentGeneration extends Model
{
    protected $fillable = ['name', 'code'];

    protected $table = 'student_generations';

    public function getLabelAttribute(): string
    {
        if (strlen($this->code) === 4 && ctype_digit($this->code)) {
            return 'SY '.substr($this->code, 0, 2).'/'.substr($this->code, 2, 2);
        }

        return $this->name;
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'generation_id');
    }

    public function distributionSchedules(): HasMany
    {
        return $this->hasMany(DistributionSchedule::class, 'generation_id');
    }

    public function sizeChangeEvents(): HasMany
    {
        return $this->hasMany(SizeChangeEvent::class, 'generation_id');
    }
}
