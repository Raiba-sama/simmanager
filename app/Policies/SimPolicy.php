<?php

namespace App\Policies;

use App\Models\Sim;
use App\Models\User;

class SimPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Sim $sim): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManageSims();
    }

    public function update(User $user, Sim $sim): bool
    {
        return $user->canManageSims();
    }

    public function delete(User $user, Sim $sim): bool
    {
        return $user->canManageSims();
    }

    public function assign(User $user, Sim $sim): bool
    {
        return $user->canManageSims();
    }

    public function unassign(User $user, Sim $sim): bool
    {
        return $user->canManageSims();
    }
}

