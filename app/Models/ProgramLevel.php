<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramLevel extends Model
{
    protected $fillable = ['name', 'code'];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(Entitlement::class);
    }
}
