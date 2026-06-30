<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DistributionSchedule extends Model
{
    protected $fillable = [
        'stage_id',
        'name',
        'date',
        'location',
        'session',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(DistributionStage::class, 'stage_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DistScheduleItem::class, 'schedule_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(DistributionTransaction::class, 'schedule_id');
    }

    public function emailNotifications(): HasMany
    {
        return $this->hasMany(EmailNotification::class, 'schedule_id');
    }
}
