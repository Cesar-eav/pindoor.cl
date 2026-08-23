<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureArtista
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->type === 'artista' || $user->artistas()->exists())) {
            return $next($request);
        }

        return redirect('/dashboard');
    }
}
