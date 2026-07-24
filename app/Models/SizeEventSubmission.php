<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SizeEventSubmission extends Model
{
    protected $fillable = [
        'student_id',
        'event_id',
        'submission_count',
    ];

    protected function casts(): array
    {
        return [
            'submission_count' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(SizeChangeEvent::class, 'event_id');
    }
}
