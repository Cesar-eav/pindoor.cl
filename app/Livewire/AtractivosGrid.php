<?php

namespace App\Livewire;

use App\Models\Artista;
use App\Models\Categoria;
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
        $proximosPanoramas = $hayFiltros ? collect() : Panorama::where('activo', true)
            ->whereNull('dias_semana')
            ->where(fn($q) => $q->whereNull('fecha_fin')->where('fecha', '>=', $hoy)
                ->orWhere('fecha_fin', '>=', $hoy))
            ->get()
            ->map(fn($p) => tap($p, fn($p) => $p->fecha_proxima = $p->proximaOcurrencia($hoy)))
            ->filter(fn($p) => $p->fecha_proxima !== null)
            ->sortBy('fecha_proxima')
            ->take(30)
            ->values();

        $ultimosPosts = $hayFiltros ? collect() : Post::publicados()->take(10)->get();

        return view('livewire.atractivos-grid', compact(
            'atractivos', 'categorias', 'hayFiltros', 'panoramas',
            'proximosPanoramas', 'ultimosPosts', 'artistas'
        ));
    }
}
