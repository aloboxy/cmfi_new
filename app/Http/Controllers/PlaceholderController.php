<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlaceholderController extends Controller
{
    public function __invoke(Request $request)
    {
        $routeName = $request->route()->getName() ?? 'module';
        $titleKey = str($routeName)->afterLast('.')->kebab()->replace('-', '_')->toString();
        // Fallback: humanize route name if translation empty
        $label = translate($titleKey);
        if ($label === $titleKey) {
            $label = str($routeName)->afterLast('.')->headline()->toString();
        }

        return view('placeholder', [
            'title' => $label,
            'routeName' => $routeName,
        ]);
    }
}
