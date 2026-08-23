<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Recomendacion;
use App\Models\Revival;

class PreviewController extends Controller
{
    // Mapa tipo de URL => [clase del modelo, vista pública, ruta de la versión en vivo]
    private const CONFIG = [
        'revival'    => [Revival::class, 'revival.show', 'revival', 'revival.show'],
        'blog'       => [Post::class, 'blog.show', 'post', 'blog.show'],
        'recomienda' => [Recomendacion::class, 'recomienda.show', 'recomendacion', 'recomienda.show'],
    ];

    /**
     * Link compartible sin sesión de admin. Sirve el borrador mientras el
     * registro no esté en vivo; en cuanto lo está, el token ya cumplió su
     * función y el link muere — redirige a la URL pública real para que sea
     * esa la que siga circulando.
     */
    public function show(string $tipo, string $token)
    {
        abort_unless(isset(self::CONFIG[$tipo]), 404);
        [$claseModelo, $vista, $variable, $rutaPublica] = self::CONFIG[$tipo];

        $registro = $claseModelo::where('preview_token', $token)->firstOrFail();

        if (! $registro->previewEstaVivo()) {
            return redirect()->route($rutaPublica, $registro->slug);
        }

        $registro->load('imagenes');
        if ($tipo === 'blog') {
            $registro->load('lugares', 'rutas');
        }

        return view($vista, [$variable => $registro]);
    }
}
