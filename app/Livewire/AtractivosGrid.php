<?php

namespace App\Livewire;

use App\Models\Artista;
use App\Models\Categoria;
use App\Models\Experiencia;
use App\Models\ModuloItem;
use App\Models\Panorama;
use App\Models\Post;
use App\Models\PuntoInteres;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AtractivosGrid extends Component
{
    use WithPagination;

    public string $search   = '';
    public string $category = '';
    public ?float $lat      = null;
    public ?float $lng      = null;

    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedCategory(): void { $this->resetPage(); }

    #[On('gps-update')]
    public function setGps(float $lat, float $lng): void
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->resetPage();
    }

    public function clearGps(): void
    {
        $this->lat = null;
        $this->lng = null;
        $this->resetPage();
    }

    public function render()
    {
        $query = PuntoInteres::query()
            ->where('activo', 1)
            ->sinExcluidos()
            ->where('eliminado', false);

        if ($this->category) {
            $query->whereHas('categoria', fn($q) => $q->where('slug', $this->category));
        }

        if ($this->search) {
            $s = $this->search;
            $query->where(fn($q) => $q
                ->where('title', 'like', "%{$s}%")
                ->orWhere('description', 'like', "%{$s}%")
                ->orWhere('descripcion_busqueda', 'like', "%{$s}%")
                ->orWhere('tags', 'like', "%{$s}%")
            );
        }

        if ($this->lat && $this->lng) {
            $query->whereNotNull('lat')->whereNotNull('lng')
                ->selectRaw('*, ST_Distance_Sphere(POINT(lng, lat), POINT(?, ?)) as distancia', [$this->lng, $this->lat])
                ->orderBy('distancia');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $atractivos = $query->with(['categoria', 'imagenPrincipal'])->paginate(48);

        $categorias = Categoria::withCount(['puntosInteres' => fn($q) => $q->where('activo', 1)->where('eliminado', false)])
            ->orderByDesc('puntos_interes_count')
            ->get();

        $hayFiltros = (bool) ($this->search || $this->category || $this->lat);

        $categoriasConPuntos = collect();
        if (!$hayFiltros) {
            $poolSize = 8 * $categorias->count();
            $puntosPool = PuntoInteres::where('activo', 1)
                ->where('eliminado', false)
                ->sinExcluidos()
                ->whereNotNull('categoria_id')
                ->with('imagenPrincipal')
                ->latest('updated_at')
                ->limit($poolSize)
                ->get()
                ->groupBy('categoria_id');

            $categoriasConPuntos = $categorias
                ->filter(fn($cat) => $puntosPool->has($cat->id))
                ->map(fn($cat) => (object) [
                    'categoria' => $cat,
                    'puntos'    => $puntosPool->get($cat->id)->take(7),
                ])
                ->values();
        }

        $panoramas = collect();
        $artistas  = collect();
        if ($this->search) {
            $s = $this->search;
            $panoramas = Panorama::where('activo', true)
                ->where('fecha', '>=', now()->toDateString())
                ->where(fn($q) => $q
                    ->where('titulo', 'like', "%{$s}%")
                    ->orWhere('ubicacion', 'like', "%{$s}%")
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
        }

        $hoy = Carbon::today();

        // Eventos de agenda de clientes → convertir a instancias Panorama para incluirlos junto a los del admin
        $eventosCliente = $hayFiltros ? collect() : ModuloItem::where('modulo', 'eventos')
            ->where('activo', true)
            ->where('fecha', '>=', $hoy)
            ->whereHas('punto', fn($q) => $q->where('activo', true)->where('eliminado', false))
            ->with('punto')
            ->get()
            ->map(fn (ModuloItem $item) => $item->comoPanorama());

        $proximosPanoramas = $hayFiltros ? collect() : Panorama::where('activo', true)
            ->whereNull('dias_semana')
            ->where(fn($q) => $q->whereNull('fecha_fin')->where('fecha', '>=', $hoy)
                ->orWhere('fecha_fin', '>=', $hoy))
            ->get()
            ->concat($eventosCliente)
            ->map(fn($p) => tap($p, fn($p) => $p->fecha_proxima = $p->proximaOcurrencia($hoy)))
            ->filter(fn($p) => $p->fecha_proxima !== null)
            ->sortBy('fecha_proxima')
            ->take(30)
            ->values();

        $ultimosPosts = $hayFiltros ? collect() : Post::publicados()->take(10)->get();

        $ultimasExperiencias = $hayFiltros ? collect() : Experiencia::activas()->take(10)->get();

        return view('livewire.atractivos-grid', compact(
            'atractivos', 'categorias', 'categoriasConPuntos', 'hayFiltros', 'panoramas',
            'proximosPanoramas', 'ultimosPosts', 'ultimasExperiencias', 'artistas'
        ));
    }
}
