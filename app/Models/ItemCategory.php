<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemCategory extends Model
{
    protected $fillable = ['label', 'code'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(ItemSize::class, 'category_item_size', 'item_category_id', 'item_size_id');
    }
}
