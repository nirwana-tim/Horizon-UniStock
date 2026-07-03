<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemVariant extends Model
{
    use SoftDeletes;
    protected $fillable = ['item_id', 'size_id', 'size', 'size_label', 'sku', 'weight'];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function itemSize(): BelongsTo
    {
        return $this->belongsTo(ItemSize::class, 'size_id');
    }

    public function stockReceiveItems(): HasMany
    {
        return $this->hasMany(StockReceiveItem::class, 'variant_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'variant_id');
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class, 'variant_id');
    }

    public function stockOpnameItems(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class, 'variant_id');
    }
}
