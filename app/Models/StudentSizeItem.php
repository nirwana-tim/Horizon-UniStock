<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentSizeItem extends Model
{
    protected $fillable = [
        'size_profile_id',
        'item_id',
        'size',
        'change_count',
    ];

    protected function casts(): array
    {
        return [
            'change_count' => 'integer',
        ];
    }

    public function sizeProfile(): BelongsTo
    {
        return $this->belongsTo(StudentSizeProfile::class, 'size_profile_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(StudentSizeHistory::class, 'size_item_id');
    }
}
