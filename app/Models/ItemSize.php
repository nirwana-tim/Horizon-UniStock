<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemSize extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'sort_order'];

    public function variants(): HasMany
    {
        return $this->hasMany(ItemVariant::class, 'size_id');
    }
}
