<?php

namespace App\Policies;

use App\Models\Entitlement;
use App\Models\User;

class EntitlementPolicy
{
    public function view(User $user, Entitlement $entitlement): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    public function update(User $user, Entitlement $entitlement): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    public function delete(User $user, Entitlement $entitlement): bool
    {
        return $user->hasRole('super_admin');
    }
}
