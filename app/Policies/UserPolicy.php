<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function manage(User $auth, User $target): bool
    {
        if ($target->hasRole('super-admin')) {
            return $auth->hasRole('super-admin') && ! $target->is($auth);
        }

        return $target->permissionNames()
            ->diff($auth->permissionNames())
            ->isEmpty();
    }
}
