<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ItemType extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'label'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'type_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ItemCategory::class, 'category_item_type', 'item_type_id', 'item_category_id');
    }
}
