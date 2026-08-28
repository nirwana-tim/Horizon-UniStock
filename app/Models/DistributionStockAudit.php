<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionStockAudit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_type',
        'item_sku',
        'item_name',
        'variant_sku',
        'old_size',
        'new_size',
        'quantity_change',
        'old_stock',
        'new_stock',
        'reference_type',
        'reference_id',
        'user_nim',
        'user_email',
        'user_name',
        'notes',
        'created_at',
    ];
}
