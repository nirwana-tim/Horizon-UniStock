<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentGeneration extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'code'];

    protected $table = 'student_generations';

    public function getLabelAttribute(): string
    {
        if (strlen($this->code) === 4 && ctype_digit($this->code)) {
            return 'SY ' . substr($this->code, 0, 2) . '/' . substr($this->code, 2, 2);
        }
        return $this->name;
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'generation_id');
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(Entitlement::class, 'generation_id');
    }
}
