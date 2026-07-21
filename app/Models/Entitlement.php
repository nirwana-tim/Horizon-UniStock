<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entitlement extends Model
{
    protected $fillable = [
        'code',
        'student_type',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getStudentTypeLabelAttribute(): string
    {
        if (! $this->student_type) {
            return '-';
        }

        return StudentType::where('kode', $this->student_type)->value('deskripsi')
            ?? $this->student_type;
    }

    public function items(): HasMany
    {
        return $this->hasMany(EntitlementItem::class, 'entitlement_id');
    }
}
