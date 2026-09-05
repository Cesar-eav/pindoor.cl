<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\PuntoInteres;
use Livewire\Component;

class SelectorEspacioCliente extends Component
{
    private const LIMITE = 10;

    public string $busqueda = '';
    public string $categoriaFiltro = '';

    public function render()
    {
        $query = PuntoInteres::where('es_cliente', false)
            ->where('eliminado', false)
            ->elegiblesParaCliente();

        if ($this->busqueda !== '') {
            // title es JSON (Spatie Translatable) y no tiene collation: LIKE normal
            // ahí compara en binario y distingue mayúsculas/minúsculas. Mismo patrón
            // que PuntoInteres::scopeBuscar() para evitarlo.
            $termino = mb_strtolower($this->busqueda);
            $query->where(fn ($q) => $q
                ->whereRaw('LOWER(title) LIKE ?', ["%{$termino}%"])
                ->orWhereRaw('LOWER(sector) LIKE ?', ["%{$termino}%"]));
        }

        if ($this->categoriaFiltro !== '') {
            $query->where('categoria_id', $this->categoriaFiltro);
        }

        $total = $query->count();

        $espacios = $query->with('categoria:id,nombre,icono')
            ->orderBy('title')
            ->limit(self::LIMITE)
            ->get();

        $categorias = Categoria::whereIn('id', PuntoInteres::CATEGORIAS_NEGOCIO)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'icono']);

        return view('livewire.selector-espacio-cliente', compact('espacios', 'total', 'categorias'));
    }
}
