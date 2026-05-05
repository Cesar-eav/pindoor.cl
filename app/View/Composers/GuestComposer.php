<?php

namespace App\View\Composers;

use App\Models\PuntoInteres;
use Illuminate\View\View;

class GuestComposer
{
    public function compose(View $view): void
    {
        $view->with('featuredPuntos', PuntoInteres::with('imagenPrincipal')
            ->whereHas('imagenPrincipal')
            ->where('activo', true)
            ->inRandomOrder()
            ->limit(6)
            ->get());
    }
}
