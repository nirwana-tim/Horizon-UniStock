<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSizeHistory extends Model
{
    protected $fillable = [
        'size_item_id',
        'old_size',
        'new_size',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function sizeItem(): BelongsTo
    {
        return $this->belongsTo(StudentSizeItem::class, 'size_item_id');
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
