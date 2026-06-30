<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DistributionTransaction extends Model
{
    protected $fillable = [
        'student_id',
        'schedule_id',
        'stage_id',
        'staff_id',
        'status',
        'pickup_time',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'pickup_time' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(DistributionSchedule::class, 'schedule_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(DistributionStage::class, 'stage_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DistributionItem::class, 'transaction_id');
    }
}
