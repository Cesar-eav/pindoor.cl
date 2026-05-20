<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->type !== $role) {
            $type = $request->user()?->type;
            if ($type === 'cliente') return redirect()->route('cliente.mis-puntos');
            if ($type === 'artista') return redirect()->route('artista.perfil');
            return redirect('/dashboard');
        }
        return $next($request);
    }
}
