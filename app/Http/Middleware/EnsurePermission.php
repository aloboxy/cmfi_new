<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    /**
     * Restrict a route by permission prefix/action, e.g. ->middleware('permission:student,is_edit')
     * Superadmins always pass (see get_permission()).
     */
    public function handle(Request $request, Closure $next, string $module, string $action = 'is_view'): Response
    {
        if (! get_permission($module, $action)) {
            set_alert('error', translate('access_denied'));

            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
