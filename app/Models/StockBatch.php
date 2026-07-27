<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'variant_id',
        'quantity_remaining',
        'unit_hpp',
        'received_date',
        'stock_receive_item_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity_remaining' => 'integer',
            'unit_hpp' => 'decimal:2',
            'received_date' => 'date',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ItemVariant::class, 'variant_id');
    }

    public function stockReceiveItem(): BelongsTo
    {
        return $this->belongsTo(StockReceiveItem::class, 'stock_receive_item_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'stock_batch_id');
    }
}
