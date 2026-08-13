<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSummary extends Model
{
    protected $fillable = [
        'student_id',
        'total_transactions',
        'total_items_received',
        'total_spend',
        'last_distribution_at',
        'last_calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'total_transactions' => 'integer',
            'total_items_received' => 'integer',
            'total_spend' => 'decimal:2',
            'last_distribution_at' => 'datetime',
            'last_calculated_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
