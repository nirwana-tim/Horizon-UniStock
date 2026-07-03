<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'code'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
