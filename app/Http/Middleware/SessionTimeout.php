<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lifetime = (int) config('session.lifetime', 120) * 60;
            $last = $request->session()->get('last_activity');
            if ($last && (time() - $last) > $lifetime) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['message' => translate('session_expired'), 'redirect' => route('login')], 401);
                }
                return redirect()->route('login')->withErrors(['email' => translate('session_expired_please_login_again')]);
            }
            $request->session()->put('last_activity', time());
            // expose remaining seconds to frontend via header for JS
            $remaining = $lifetime - (time() - (int) $request->session()->get('last_activity', time()));
        }

        $response = $next($request);

        if (Auth::check() && isset($remaining)) {
            $response->headers->set('X-Session-Lifetime', (string) config('session.lifetime'));
            $response->headers->set('X-Session-Remaining', (string) max(0, $remaining));
        }

        return $response;
    }
}
