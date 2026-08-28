<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemSize extends Model
{
    protected $fillable = ['code', 'label', 'is_baju', 'is_sepatu'];

    protected function casts(): array
    {
        return [
            'is_baju' => 'boolean',
            'is_sepatu' => 'boolean',
        ];
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ItemVariant::class, 'size_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ItemCategory::class, 'category_item_size', 'item_size_id', 'item_category_id');
    }
}
