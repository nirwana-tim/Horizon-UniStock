<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DistributionPeriod extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'size_change_deadline',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'size_change_deadline' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function eligibilityRecords(): HasMany
    {
        return $this->hasMany(EligibilityRecord::class, 'period_id');
    }

    public function distributionStages(): HasMany
    {
        return $this->hasMany(DistributionStage::class, 'period_id');
    }

    public function studentSizeProfiles(): HasMany
    {
        return $this->hasMany(StudentSizeProfile::class, 'period_id');
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(Entitlement::class, 'period_id');
    }
}
