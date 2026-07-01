<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPrice extends Model
{
    protected $fillable = [
        'item_id',
        'period_id',
        'selling_price',
        'hpp',
        'effective_date',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'hpp' => 'decimal:2',
            'effective_date' => 'date',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(DistributionPeriod::class);
    }
}
