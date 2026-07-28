<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // El archivo excede post_max_size del servidor: PHP corta la petición
        // antes de que Laravel pueda validar el campo, así que sin esto el
        // usuario solo veía un 413 genérico (o un redirect sin explicación).
        $this->renderable(function (PostTooLargeException $e, Request $request) {
            $limite = ini_get('post_max_size') ?: 'el límite del servidor';
            $mensaje = "El archivo es demasiado pesado para subirlo (máx. {$limite}). Prueba con una imagen más liviana.";

            if ($request->expectsJson()) {
                return response()->json(['message' => $mensaje], 413);
            }
            return back()->with('error', $mensaje)->withInput($request->except(['imagen', 'imagen_portada', 'imagenes']));
        });
    }
}
