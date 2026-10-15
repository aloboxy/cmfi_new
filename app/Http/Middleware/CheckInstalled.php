<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $installed = File::exists(storage_path('installed')) || File::exists(storage_path('framework/installed'));

        // Also check DB: if global_settings has institute_name, consider installed
        if (!$installed) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('global_settings')) {
                    $has = \Illuminate\Support\Facades\DB::table('global_settings')->where('id', 1)->exists();
                    if ($has) $installed = true;
                }
            } catch (\Throwable $e) {}
        }

        $isInstallRoute = $request->is('install') || $request->is('install/*');

        if (!$installed && !$isInstallRoute) {
            return redirect('/install');
        }

        if ($installed && $isInstallRoute && !$request->has('force')) {
            return redirect('/');
        }

        return $next($request);
    }
}
