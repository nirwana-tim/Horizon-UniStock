<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    protected $fillable = [
        'reference_number',
        'opname_date',
        'period',
        'notes',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'opname_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class, 'stock_opname_id');
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(StockOpnameAdjustment::class, 'stock_opname_id');
    }
}
