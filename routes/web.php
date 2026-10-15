<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\PlaceholderController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication (legacy: authentication/*)
|--------------------------------------------------------------------------
*/

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::match(['get','post'], '/install', [InstallController::class, 'index'])->name('install.index');

/*
|--------------------------------------------------------------------------
| Authenticated area (legacy: Admin_Controller / User_Controller)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* switch active academic session (legacy: sessions/set_academic) */
    Route::get('/academic-session/{id}', function (int $id) {
        abort_unless(DB::table('schoolyear')->where('id', $id)->exists(), 404);

        session(['set_session_id' => $id]);
        set_alert('success', translate('session_changed_successfully'));

        return back();
    })->name('sessions.set');

    // Load real module routes first — they take precedence over placeholders
    foreach (glob(__DIR__.'/modules/*.php') as $file) {
        require $file;
    }
    // Refresh name lookups so Route::has works inside this group closure
    \Illuminate\Support\Facades\Route::getRoutes()->refreshNameLookups();

    // ------------------------------------------------------------------
    // Placeholders so every remaining sidebar link is navigable until
    // its module is converted. Each real module replaces its placeholder.
    // ------------------------------------------------------------------
    $collect = function (array $items) use (&$collect): array {
        $out = [];
        foreach ($items as $it) {
            if (isset($it['route'])) {
                $out[] = $it['route'];
            }
            if (isset($it['children'])) {
                $out = array_merge($out, $collect($it['children']));
            }
        }

        return $out;
    };
    $allNavRoutes = $collect(config('navigation', []));

    foreach (array_unique($allNavRoutes) as $name) {
        if (Route::has($name)) {
            continue;
        }
        $uri = '/'.str_replace(['.', '_'], ['/', '-'], $name);
        $uri = preg_replace('#/index$#', '', $uri);
        if ($uri === '/dashboard') {
            continue;
        }
        Route::get($uri, PlaceholderController::class)->name($name);
    }
});
