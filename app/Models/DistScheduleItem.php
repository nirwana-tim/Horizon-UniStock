<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistScheduleItem extends Model
{
    protected $fillable = ['schedule_id', 'item_id'];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(DistributionSchedule::class, 'schedule_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
