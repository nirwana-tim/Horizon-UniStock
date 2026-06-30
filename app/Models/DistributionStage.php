<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DistributionStage extends Model
{
    protected $fillable = [
        'period_id',
        'name',
        'stage_order',
        'start_date',
        'end_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(DistributionPeriod::class, 'period_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DistributionSchedule::class, 'stage_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(DistributionTransaction::class, 'stage_id');
    }
}
