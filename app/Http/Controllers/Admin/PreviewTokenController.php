<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Recomendacion;
use App\Models\Revival;

class PreviewTokenController extends Controller
{
    private const MODELOS = [
        'revival'    => Revival::class,
        'blog'       => Post::class,
        'recomienda' => Recomendacion::class,
    ];

    /** Mata el link de preview viejo (por si se filtró) y deja uno nuevo. */
    public function regenerar(string $tipo, int $id)
    {
        abort_unless(isset(self::MODELOS[$tipo]), 404);

        $registro = self::MODELOS[$tipo]::findOrFail($id);
        $registro->regenerarPreviewToken();

        return back()->with('success', 'Link de vista previa regenerado — el anterior ya no funciona.');
    }
}
