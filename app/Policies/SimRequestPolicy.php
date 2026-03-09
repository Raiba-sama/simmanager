<?php

namespace App\Policies;

use App\Models\SimRequest;
use App\Models\User;

class SimRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SimRequest $request): bool
    {
        return $user->id === $request->user_id || $user->canValidateRequests();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, SimRequest $request): bool
    {
        // L'admin peut toujours modifier (ex. changer la carte SIM puis renvoyer au webhook)
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $request->user_id || $user->canValidateRequests();
    }

    public function delete(User $user, SimRequest $request): bool
    {
        return $user->isAdmin();
    }

    public function validateRequest(User $user, SimRequest $request): bool
    {
        return $user->canValidateRequests() && $request->isEnAttente();
    }

    public function rejectRequest(User $user, SimRequest $request): bool
    {
        return $user->canValidateRequests() && $request->isEnAttente();
    }
}

