<?php

namespace App\Services\System;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Roles that can be managed through the account management UI.
     * super_admin (always invisible) and student (managed elsewhere) are excluded.
     */
    public const MANAGEABLE_ROLES = ['admin', 'staff'];

    /**
     * A user may only be managed if it has one of the manageable roles.
     */
    public function allowedRoles(): array
    {
        return self::MANAGEABLE_ROLES;
    }

    /**
     * The query scope visible to the actor: only admin/staff accounts.
     */
    public function manageableQuery(): Builder
    {
        return User::role(self::MANAGEABLE_ROLES)
            ->where('id', '!=', Auth::id());
    }

    /**
     * Create an account. Roles are validated against the whitelist.
     */
    public function store(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'must_change_password' => true,
            'is_active' => true,
        ]);

        $user->assignRole($data['role']);

        return $user;
    }

    /**
     * Update an existing managed account.
     */
    public function update(User $user, array $data): User
    {
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        if (isset($data['email'])) {
            $user->email = $data['email'];
        }
        if (! empty($data['password'])) {
            $user->password = $data['password'];
            $user->must_change_password = true;
        }

        if (isset($data['role']) && $data['role'] !== $user->getRoleNames()->first()) {
            $user->syncRoles([$data['role']]);
        }

        $user->save();

        return $user;
    }

    /**
     * Toggle activation status. Blocked on self.
     */
    public function setActive(User $user, bool $active): User
    {
        if ($user->id === Auth::id()) {
            return $user;
        }

        $user->is_active = $active;
        $user->save();

        return $user;
    }
}
