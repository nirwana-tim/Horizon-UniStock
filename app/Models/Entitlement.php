<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entitlement extends Model
{
    protected $fillable = [
        'code',
        'student_level',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getStudentLevelLabelAttribute(): string
    {
        return $this->studentLevel?->deskripsi ?? $this->student_level ?? '-';
    }

    public function items(): HasMany
    {
        return $this->hasMany(EntitlementItem::class, 'entitlement_id');
    }

    public function studentLevel(): BelongsTo
    {
        return $this->belongsTo(StudentLevel::class, 'student_level', 'kode');
    }
}
