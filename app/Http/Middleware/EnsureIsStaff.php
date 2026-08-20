<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates every /admin/* route (plan §7 Phase 5). Runs after `auth` (see
 * routes/web.php's admin group), so by the time this executes we already
 * have a signed-in user — this just checks their Role is Staff or Admin.
 * A non-staff signed-in user gets a 403, not a redirect to login (they
 * *are* logged in, they just can't be here).
 */
class EnsureIsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->user()?->role;

        abort_unless(in_array($role, [Role::Staff, Role::Admin], true), 403);

        return $next($request);
    }
}
