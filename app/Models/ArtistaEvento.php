<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ArtistaEvento extends Model
{
    use HasTranslations;

    public array $translatable = ['titulo', 'descripcion'];

    public function getFallbackLocale(?string $locale = null): ?string
    {
        return $locale !== 'es' ? 'es' : null;
    }

    protected $fillable = [
        'artista_id',
        'titulo',
        'tipo',
        'descripcion',
        'fecha',
        'hora',
        'hora_fin',
        'precio',
        'precio_texto',
        'url_entradas',
        'imagen',
        'activo',
        'destacado',
        'orden',
    ];

    protected $casts = [
        'activo'    => 'boolean',
        'destacado' => 'boolean',
        'fecha'     => 'date',
    ];

    public function artista()
    {
        return $this->belongsTo(Artista::class);
    }

    /**
     * Texto de precio. Prioriza precio_texto libre; si no, formatea el número;
     * si es null → "Consultar". Mismo criterio que ModuloItem::precioEvento().
     */
    public function precioEvento(): string
    {
        if ($textoLibre = $this->precio_texto) {
            return $textoLibre;
        }
        if ($this->precio === null) {
            return 'Consultar';
        }
        if ((float) $this->precio === 0.0) {
            return 'Entrada liberada';
        }
        return '$' . number_format((float) $this->precio, 0, ',', '.');
    }

    /** Info del tipo de evento (emoji + label). */
    public function tipoEvento(): array
    {
        return CategoriaEvento::catalogo()[$this->tipo] ?? ['emoji' => '📌', 'label' => $this->tipo];
    }

    /**
     * Convierte un evento de artista en una instancia Panorama de solo lectura,
     * para reutilizar la vista/lógica ya construida para panoramas (panoramas.show,
     * panoramas.index, home). Mismo patrón que ModuloItem::comoPanorama().
     *
     * OJO: 'id' se castea a int automáticamente por ser la primary key de Panorama, así
     * que cualquier id sintético no numérico ('aev_5') se lee como 0 — no usar $fake->id
     * para nada que dependa de que sea único. El id real va aparte, en 'modulo_item_id'.
     * Para combinar esta instancia con una colección de Panorama reales hay que usar
     * ->concat(), nunca ->merge() (que en Eloquent\Collection dedupea por getKey()).
     */
    public function comoPanorama(): Panorama
    {
        $fake = new Panorama();
        $fake->fill([
            'titulo'      => $this->titulo,
            'descripcion' => $this->descripcion ?? '',
            'ubicacion'   => $this->artista?->nombre,
            'fecha'       => $this->fecha->format('Y-m-d'),
            'fecha_fin'   => null,
            'dias_semana' => null,
            'hora'        => $this->hora,
            'categoria'   => $this->tipo,
            'es_gratuito' => (float) ($this->precio ?? 1) === 0.0,
            'enlace'      => $this->url_entradas,
            'imagen'      => $this->imagen,
            'activo'      => true,
        ]);
        $fake->setAttribute('id', 'aev_' . $this->id);
        $fake->setAttribute('modulo_item_id', $this->id);
        $fake->setAttribute('artista_slug', $this->artista?->slug);
        $fake->setRelation('imagenes', collect());

        return $fake;
    }
}
