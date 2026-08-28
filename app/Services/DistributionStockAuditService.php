<?php

namespace App\Services;

use App\Models\DistributionStockAudit;

class DistributionStockAuditService
{
    public static function log(array $data): DistributionStockAudit
    {
        $user = auth()->user();

        if ($user && ! isset($data['user_nim'])) {
            $student = $user->student;
            $data['user_nim'] = $student?->nim ?? '-';
            $data['user_email'] = $user->email ?? '-';
            $data['user_name'] = $user->name ?? '-';
        }

        $data['created_at'] = $data['created_at'] ?? now();

        return DistributionStockAudit::create($data);
    }
}
