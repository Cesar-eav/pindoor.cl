<?php

namespace App\Livewire;

use App\Http\Controllers\PuntoInteresController;
use App\Models\Artista;
use App\Models\Categoria;
use App\Models\Experiencia;
use App\Models\ModuloItem;
use App\Models\OperadorTuristico;
use App\Models\Panorama;
use App\Models\Post;
use App\Models\PuntoInteres;
use App\Models\Ruta;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AtractivosGrid extends Component
{
    use WithPagination;

    public string $search   = '';
    public string $category = '';
    public string $grupo    = '';

    public function mount(): void
    {
        $this->search   = (string) request('search', '');
        $this->category = (string) request('category', '');
        $this->grupo    = (string) request('grupo', '');
    }

    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedCategory(): void { $this->resetPage(); }
    public function updatedGrupo(): void    { $this->resetPage(); }

    #[On('search-updated')]
    public function setBusqueda(string $value): void
    {
        $this->search = $value;
    }

    #[On('category-updated')]
    public function setCategoria(string $slug): void
    {
        $this->category = $slug;
        $this->grupo    = '';
    }

    /** Íconos de grupo en la barra unificada (Atractivos / Comida y tragos / Cultura / Otros). */
    #[On('grupo-updated')]
    public function setGrupo(string $slug): void
    {
        $this->grupo    = $slug;
        $this->category = '';
    }

    public function render()
    {
        $query = PuntoInteres::publico();

        if ($this->category) {
            $query->whereHas('categoria', fn($q) => $q->where('slug', $this->category));
        } elseif ($this->grupo) {
            $query->whereHas('categoria.grupo', fn($q) => $q->where('slug', $this->grupo));
        }

        if ($this->search) {
            $query->buscar($this->search);
        }

        $query->orderBy('updated_at', 'desc');

        $atractivos = $query->with(['categoria', 'imagenPrincipal'])->paginate(48);

        $categorias = Categoria::withCount(['puntosInteres' => fn($q) => $q->publico()])
            ->orderByDesc('puntos_interes_count')
            ->get();

        $hayFiltros = (bool) ($this->search || $this->category || $this->grupo);

        $categoriasConPuntos = collect();
        if (!$hayFiltros) {
            $porCategoria = PuntoInteresController::PUNTOS_POR_CATEGORIA;
            $categoriasConPuntos = $categorias
                ->map(fn($cat) => (object) [
                    'categoria' => $cat,
                    'puntos'    => PuntoInteres::publico()
                        ->where('categoria_id', $cat->id)
                        ->when($cat->slug === 'ascensores', fn($q) => $q->where('fuera_de_servicio', false))
                        ->with('imagenPrincipal')
                        ->orderByDesc('es_cliente')
                        ->latest('updated_at')
                        ->limit($porCategoria)
                        ->get(),
                ])
                ->filter(fn($entry) => $entry->puntos->isNotEmpty())
                ->values();
        }

        $panoramas = collect();
        $artistas  = collect();
        $operadores = collect();
        if ($this->search) {
            $s = $this->search;
            $sLower = mb_strtolower($s);
            $panoramas = Panorama::where('activo', true)
                ->where('fecha', '>=', now()->toDateString())
                ->where(fn($q) => $q
                    ->whereRaw('LOWER(titulo) LIKE ?', ["%{$sLower}%"])
                    ->orWhereRaw('LOWER(ubicacion) LIKE ?', ["%{$sLower}%"])
                )
                ->orderBy('fecha')
                ->limit(6)
                ->get();

            $artistas = Artista::where('activo', true)
                ->where(fn($q) => $q
                    ->where('nombre', 'like', "%{$s}%")
                    ->orWhere('descripcion', 'like', "%{$s}%")
                    ->orWhere('disciplina', 'like', "%{$s}%")
                )
                ->limit(6)
                ->get();

            $operadores = OperadorTuristico::where('activo', true)
                ->where(fn($q) => $q
                    ->where('nombre', 'like', "%{$s}%")
                    ->orWhere('descripcion', 'like', "%{$s}%")
                    ->orWhere('ciudad', 'like', "%{$s}%")
                )
                ->limit(6)
                ->get();
        }

        $hoy = Carbon::today();

        // Eventos de agenda de clientes → convertir a instancias Panorama para incluirlos junto a los del admin
        $eventosCliente = $hayFiltros ? collect() : ModuloItem::where('modulo', 'eventos')
            ->where('activo', true)
            ->where('fecha', '>=', $hoy)
            ->whereHas('punto', fn($q) => $q->publico())
            ->with('punto')
            ->get()
            ->map(fn (ModuloItem $item) => $item->comoPanorama());

        // Eventos de artistas → mismo tratamiento
        $eventosArtista = $hayFiltros ? collect() : \App\Models\ArtistaEvento::where('activo', true)
            ->where('fecha', '>=', $hoy)
            ->whereHas('artista', fn($q) => $q->where('activo', true))
            ->with('artista')
            ->get()
            ->map(fn (\App\Models\ArtistaEvento $item) => $item->comoPanorama());

        $proximosPanoramas = $hayFiltros ? collect() : Panorama::where('activo', true)
            ->whereNull('dias_semana')
            ->where(fn($q) => $q->whereNull('fecha_fin')->where('fecha', '>=', $hoy)
                ->orWhere('fecha_fin', '>=', $hoy))
            ->get()
            ->concat($eventosCliente)
            ->concat($eventosArtista)
            ->map(fn($p) => tap($p, fn($p) => $p->fecha_proxima = $p->proximaOcurrencia($hoy)))
            ->filter(fn($p) => $p->fecha_proxima !== null)
            ->sortBy('fecha_proxima')
            ->take(30)
            ->values();

        $ultimosPosts = $hayFiltros ? collect() : Post::publicados()->take(10)->get();

        $ultimasRutas = $hayFiltros ? collect() : Ruta::publicadas()->with('puntos')->take(10)->get();

        $ultimasExperiencias = $hayFiltros ? collect() : Experiencia::activas()->take(10)->get();

        return view('livewire.atractivos-grid', compact(
            'atractivos', 'categorias', 'categoriasConPuntos', 'hayFiltros', 'panoramas',
            'proximosPanoramas', 'ultimosPosts', 'ultimasRutas', 'ultimasExperiencias', 'artistas', 'operadores'
        ));
    }
}
