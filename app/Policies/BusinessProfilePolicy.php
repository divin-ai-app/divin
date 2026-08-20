<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\BusinessProfile;
use App\Models\ProfileOwnership;
use App\Models\User;

/**
 * Dashboard access control — auto-discovered by Laravel via the
 * App\Models\BusinessProfile <-> App\Policies\BusinessProfilePolicy naming
 * convention. ClaimController has its own inline ownership check (it runs
 * before ownership exists), but everything under /dashboard/{profile}
 * uses this via the `can:manage,profile` route middleware.
 *
 * Staff/Admin also pass: Phase 5's admin profile detail view links straight
 * into the existing dashboard edit form rather than duplicating it, so
 * staff need the same `manage` ability an owner has, for any profile.
 */
class BusinessProfilePolicy
{
    public function manage(User $user, BusinessProfile $profile): bool
    {
        if (in_array($user->role, [Role::Staff, Role::Admin], true)) {
            return true;
        }

        return ProfileOwnership::query()
            ->where('user_id', $user->id)
            ->where('profile_id', $profile->id)
            ->exists();
    }
}
