<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentSizeProfile extends Model
{
    protected $fillable = [
        'student_id',
        'period_id',
        'is_filled',
        'filled_at',
    ];

    protected function casts(): array
    {
        return [
            'is_filled' => 'boolean',
            'filled_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(DistributionPeriod::class, 'period_id');
    }

    public function sizeItems(): HasMany
    {
        return $this->hasMany(StudentSizeItem::class, 'size_profile_id');
    }
}
