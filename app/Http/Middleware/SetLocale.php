<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supported = ['es', 'en'];

        // 1. Segmento de URL: /en/...
        $segment = $request->segment(1);
        if (in_array($segment, $supported)) {
            App::setLocale($segment);
            session(['locale' => $segment]);
            return $next($request);
        }

        // 2. Sesión persistida
        $locale = session('locale');
        if ($locale && in_array($locale, $supported)) {
            App::setLocale($locale);
            return $next($request);
        }

        // 3. Default: español
        App::setLocale('es');
        return $next($request);
    }
}
