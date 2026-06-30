<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReceiveItem extends Model
{
    protected $fillable = [
        'stock_receive_id',
        'item_id',
        'variant_id',
        'quantity',
        'unit_price',
        'hpp',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'hpp' => 'decimal:2',
        ];
    }

    public function stockReceive(): BelongsTo
    {
        return $this->belongsTo(StockReceive::class, 'stock_receive_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ItemVariant::class, 'variant_id');
    }
}
