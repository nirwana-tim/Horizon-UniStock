<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ItemCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['label', 'code'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(ItemSize::class, 'category_item_size', 'item_category_id', 'item_size_id');
    }

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(ItemType::class, 'category_item_type', 'item_category_id', 'item_type_id');
    }
}
