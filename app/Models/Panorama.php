<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Panorama extends Model
{
    use HasTranslations;

    public array $translatable = ['titulo', 'ubicacion', 'descripcion'];

    const FUENTES = [
        'manual'     => ['label' => 'Manual',     'emoji' => '✍️'],
        'passline'   => ['label' => 'Passline',   'emoji' => '🎫'],
        'portaldisc' => ['label' => 'Portaldisc', 'emoji' => '💿'],
        'cliente'    => ['label' => 'Cliente',    'emoji' => '🏢'],
    ];

    const DIAS = [
        1 => 'Lun', 2 => 'Mar', 3 => 'Mié',
        4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom',
    ];

    const SEMANAS_MES = [
        1  => 'Primer(a)',
        2  => 'Segundo(a)',
        3  => 'Tercer(a)',
        4  => 'Cuarto(a)',
        -1 => 'Último(a)',
    ];

    protected $fillable = [
        'titulo',
        'slug',
        'ubicacion',
        'descripcion',
        'fecha',
        'fecha_fin',
        'dias_semana',
        'semana_del_mes',
        'hora',
        'categoria',
        'es_gratuito',
        'enlace',
        'fuente',
        'fuente_id',
        'created_by',
        'imagen',
        'activo',
        'orden',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (self $p) {
            $p->slug = Str::slug($p->titulo) . '-' . uniqid();
            if (auth()->check() && !$p->created_by) {
                $p->created_by = auth()->user()->name;
            }
        });

        static::created(function (self $p) {
            $p->slug = Str::slug($p->titulo) . '-' . $p->id;
            $p->saveQuietly();
        });
    }

    protected $casts = [
        'fecha'       => 'date',
        'fecha_fin'   => 'date',
        'dias_semana'    => 'array',
        'semana_del_mes' => 'integer',
        'activo'      => 'boolean',
        'es_gratuito' => 'boolean',
    ];

    public function imagenes()
    {
        return $this->hasMany(PanoramaImagen::class)->orderBy('orden');
    }

    public function proximaOcurrencia(Carbon $desde): ?Carbon
    {
        if (empty($this->dias_semana)) {
            return $this->fecha->gte($desde) ? $this->fecha->copy() : null;
        }

        $fin    = $this->fecha_fin ?? $this->fecha;
        $cursor = $desde->copy();

        while ($cursor->lte($fin)) {
            if (in_array($cursor->isoWeekday(), $this->dias_semana) && $this->coincideSemanaDelMes($cursor)) {
                return $cursor;
            }
            $cursor->addDay();
        }

        return null;
    }

    /**
     * Si semana_del_mes está definido, filtra para que solo coincida esa semana del mes
     * (1-4 = primera..cuarta ocurrencia de ese día de semana en el mes; -1 = la última).
     * Null = sin filtro, coincide cualquier semana (comportamiento semanal normal).
     */
    public function coincideSemanaDelMes(Carbon $fecha): bool
    {
        if ($this->semana_del_mes === null) {
            return true;
        }

        if ($this->semana_del_mes === -1) {
            return $fecha->copy()->addDays(7)->month !== $fecha->month;
        }

        $ocurrencia = intdiv($fecha->day - 1, 7) + 1;
        return $ocurrencia === $this->semana_del_mes;
    }

    /**
     * Próximos panoramas reales de la misma categoría, para la sección "relacionados"
     * en la vista de detalle. $excluirId se pasa solo cuando el panorama actual es real
     * (tiene id propio) — los eventos de agenda de cliente/artista no tienen id real acá.
     */
    public static function relacionados(?string $categoria, ?int $excluirId = null, int $limite = 20): \Illuminate\Support\Collection
    {
        if (!$categoria) {
            return collect();
        }

        $hoy = Carbon::today();

        return static::where('activo', true)
            ->where('categoria', $categoria)
            ->when($excluirId, fn($q) => $q->where('id', '!=', $excluirId))
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')->where('fecha', '>=', $hoy)
                  ->orWhere('fecha_fin', '>=', $hoy);
            })
            ->get()
            ->map(function ($p) use ($hoy) {
                $p->fecha_proxima = $p->proximaOcurrencia($hoy);
                return $p;
            })
            ->filter(fn($p) => $p->fecha_proxima !== null)
            ->sortBy('fecha_proxima')
            ->take($limite)
            ->values();
    }

    public function scopeActivos($query, $limite = 15)
    {
        $hoy   = Carbon::today();
        $hasta = Carbon::today()->addDays($limite);

        return $query->where('activo', true)
            ->where('fecha', '<=', $hasta)
            ->where(function ($q) use ($hoy) {
                // Eventos sin fecha_fin: que inicien desde hoy
                // Eventos con fecha_fin: que aún no hayan terminado
                $q->whereNull('fecha_fin')->where('fecha', '>=', $hoy)
                  ->orWhere('fecha_fin', '>=', $hoy);
            });
    }
}
