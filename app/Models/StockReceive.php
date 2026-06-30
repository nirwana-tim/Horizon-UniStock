<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockReceive extends Model
{
    protected $fillable = [
        'reference_number',
        'vendor_id',
        'receive_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'receive_date' => 'date',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockReceiveItem::class, 'stock_receive_id');
    }
}
