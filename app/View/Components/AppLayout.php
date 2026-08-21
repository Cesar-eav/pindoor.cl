<?php

namespace App\View\Components;

use App\Models\PuntoInteres;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * @param PuntoInteres|null $punto Negocio del cliente en pantalla, usado por el menú
     *   contextual del panel (layouts.navigation) para mostrar sus secciones.
     */
    public function __construct(
        public ?PuntoInteres $punto = null,
        public array $modulos = [],
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
