<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ItemSize extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'label'];

    public function variants(): HasMany
    {
        return $this->hasMany(ItemVariant::class, 'size_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ItemCategory::class, 'category_item_size', 'item_size_id', 'item_category_id');
    }
}
