<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $fillable = ['name', 'email', 'contact', 'phone'];

    public function stockReceives(): HasMany
    {
        return $this->hasMany(StockReceive::class);
    }
}
