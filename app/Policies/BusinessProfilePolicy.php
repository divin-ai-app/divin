<?php

namespace App\Policies;

use App\Models\BusinessProfile;
use App\Models\ProfileOwnership;
use App\Models\User;

/**
 * Dashboard access control — auto-discovered by Laravel via the
 * App\Models\BusinessProfile <-> App\Policies\BusinessProfilePolicy naming
 * convention. ClaimController has its own inline ownership check (it runs
 * before ownership exists), but everything under /dashboard/{profile}
 * uses this via the `can:manage,profile` route middleware.
 */
class BusinessProfilePolicy
{
    public function manage(User $user, BusinessProfile $profile): bool
    {
        return ProfileOwnership::query()
            ->where('user_id', $user->id)
            ->where('profile_id', $profile->id)
            ->exists();
    }
}
