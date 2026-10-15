<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Restrict a route to specific role IDs, e.g. ->middleware('role:1,2')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_unless(in_array(loggedin_role_id(), array_map('intval', $roles), true), 403, translate('access_denied'));

        return $next($request);
    }
}
