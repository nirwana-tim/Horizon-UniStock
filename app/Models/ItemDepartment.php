<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemDepartment extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'label'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'department_id');
    }
}
