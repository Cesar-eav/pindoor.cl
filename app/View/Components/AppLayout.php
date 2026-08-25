<?php

namespace App\View\Components;

use App\Models\PuntoInteres;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public ?PuntoInteres $punto;
    public array $modulos;

    /**
     * $punto va sin type-hint de clase a propósito: si el componente se usa como
     * <x-app-layout> sin :punto, el contenedor de Laravel resuelve un parámetro
     * de constructor tipado como PuntoInteres instanciando uno vacío (id null)
     * en vez de usar el default null, y layouts.navigation revienta al armar
     * route('cliente.perfil.ver', $punto).
     */
    public function __construct($punto = null, array $modulos = [])
    {
        $this->punto = $punto;
        $this->modulos = $modulos;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
