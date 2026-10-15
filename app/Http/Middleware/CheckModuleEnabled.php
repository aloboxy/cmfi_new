<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleEnabled
{
    public function handle(Request $request, Closure $next, ?string $module = null): Response
    {
        // Super admin bypasses module check to be able to re-enable
        if (is_superadmin_loggedin()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        // If explicit module key passed via middleware parameter, check it
        if ($module) {
            if (!is_module_enabled($module)) {
                return $this->blockedResponse($request, $module);
            }
            return $next($request);
        }

        // Otherwise, infer module from route name via navigation config
        if ($routeName) {
            $moduleKey = $this->routeToModuleKey($routeName);
            if ($moduleKey && !is_module_enabled($moduleKey)) {
                return $this->blockedResponse($request, $moduleKey);
            }
        }

        return $next($request);
    }

    private function routeToModuleKey(string $routeName): ?string
    {
        $nav = config('navigation', []);
        $map = $this->buildRouteMap($nav);
        return $map[$routeName] ?? null;
    }

    private function buildRouteMap(array $items, ?string $parent = null): array
    {
        $map = [];
        foreach ($items as $it) {
            $labelKey = isset($it['label']) ? strtolower(str_replace([' ', '-'], '_', trim($it['label']))) : null;
            if (isset($it['route'])) {
                $map[$it['route']] = $labelKey ?? $it['route'];
            }
            if (!empty($it['children'])) {
                $map = array_merge($map, $this->buildRouteMap($it['children'], $labelKey));
            }
        }
        return $map;
    }

    private function blockedResponse(Request $request, string $module): Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => translate('module_disabled').': '.$module, 'module' => $module], 403);
        }
        set_alert('error', translate('module_disabled').' — '.ucwords(str_replace('_',' ', $module)));
        return redirect()->route('dashboard');
    }
}
