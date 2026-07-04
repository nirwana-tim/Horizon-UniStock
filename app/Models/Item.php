<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use SoftDeletes;

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    protected $fillable = [
        'name',
        'code',
        'gender',
        'category_id',
        'type_id',
        'department_id',
        'unit',
        'selling_price',
        'hpp',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'hpp' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ItemType::class, 'type_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(ItemDepartment::class, 'department_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ItemVariant::class);
    }

    public function entitlementItems(): HasMany
    {
        return $this->hasMany(EntitlementItem::class);
    }

    public function stockReceiveItems(): HasMany
    {
        return $this->hasMany(StockReceiveItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    public function sizeItems(): HasMany
    {
        return $this->hasMany(StudentSizeItem::class);
    }

    public function distScheduleItems(): HasMany
    {
        return $this->hasMany(DistScheduleItem::class);
    }

    public function distributionItems(): HasMany
    {
        return $this->hasMany(DistributionItem::class);
    }

    public function stockOpnameItems(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ItemPrice::class);
    }

    public function currentPrice(): ?ItemPrice
    {
        return $this->prices()->latest('id')->first();
    }
}
