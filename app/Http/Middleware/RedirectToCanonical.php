<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectToCanonical
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('local')) {
            return $next($request);
        }

        $host = $request->getHost();

        if (str_starts_with($host, 'www.')) {
            $canonical = 'https://' . substr($host, 4) . $request->getRequestUri();
            return redirect($canonical, 301);
        }

        if (! $request->isSecure()) {
            return redirect('https://' . $host . $request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
