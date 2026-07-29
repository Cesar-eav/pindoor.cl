<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Almacena ítems de lista de un módulo para un punto de interés.
 * Múltiples registros por punto+módulo.
 *
 * Módulos soportados: 'entradas', 'exposiciones', 'eventos', 'habitaciones', 'productos', …
 */
class ModuloItem extends Model
{
    protected $table = 'punto_modulo_items';

    protected $fillable = [
        'punto_interes_id',
        'modulo',
        'datos',
        'imagen',
        'activo',
        'orden',
        'destacado',
        'fecha',
    ];

    protected $casts = [
        'datos'     => 'array',
        'activo'    => 'boolean',
        'destacado' => 'boolean',
        'fecha'     => 'date',
    ];

    // ─── Relaciones ────────────────────────────────────────────────────────────

    public function punto()
    {
        return $this->belongsTo(PuntoInteres::class, 'punto_interes_id');
    }

    // ─── Helpers de presentación ───────────────────────────────────────────────

    /**
     * Devuelve un campo del JSON de datos, o $defecto si no existe.
     */
    public function campo(string $clave, mixed $defecto = null): mixed
    {
        return $this->datos[$clave] ?? $defecto;
    }

    /**
     * Precio formateado para módulo 'entradas'.
     * Si precio == 0 devuelve "Gratuito".
     */
    public function precioEntrada(): string
    {
        $precio = (float) ($this->datos['precio'] ?? 0);
        if ($precio === 0.0) {
            return 'Gratuito';
        }
        return '$' . number_format($precio, 0, ',', '.');
    }

    /**
     * Texto de precio para módulo 'eventos'.
     * Prioriza precio_texto libre; si no, formatea el número; si es null → "Consultar".
     */
    public function precioEvento(): string
    {
        if ($textoLibre = $this->datos['precio_texto'] ?? null) {
            return $textoLibre;
        }
        $precio = $this->datos['precio'] ?? null;
        if ($precio === null) {
            return 'Consultar';
        }
        if ((float) $precio === 0.0) {
            return 'Entrada liberada';
        }
        return '$' . number_format((float) $precio, 0, ',', '.');
    }

    /**
     * Info del tipo de evento (emoji + label) para módulo 'eventos'.
     */
    public function tipoEvento(): array
    {
        $tipo = $this->datos['tipo'] ?? 'otros';
        return CategoriaEvento::catalogo()[$tipo] ?? ['emoji' => '📌', 'label' => $tipo];
    }

    /**
     * Convierte un evento de agenda de cliente (módulo 'eventos') en una instancia
     * Panorama de solo lectura, para reutilizar la vista/lógica ya construida para
     * panoramas (panoramas.show, panoramas.index, home). Pensado para usarse solo
     * con items del módulo 'eventos'.
     *
     * OJO: 'id' se castea a int automáticamente por ser la primary key de Panorama, así
     * que cualquier id sintético no numérico ('ev_5') se lee como 0 — no usar $fake->id
     * para nada que dependa de que sea único (ver PANORAMAS-EVENTOS-CLIENTE-BUG.md). El
     * id real del ModuloItem va aparte, en 'modulo_item_id'. Por el mismo motivo, para
     * combinar esta instancia con una colección de Panorama reales hay que usar
     * ->concat(), nunca ->merge() (que en Eloquent\Collection dedupea por getKey()).
     */
    public function comoPanorama(): Panorama
    {
        $fake = new Panorama();
        $fake->fill([
            'titulo'      => $this->campo('titulo', ''),
            'descripcion' => $this->campo('descripcion', ''),
            'ubicacion'   => $this->punto?->title,
            'fecha'       => $this->fecha->format('Y-m-d'),
            'fecha_fin'   => null,
            'dias_semana' => null,
            'hora'        => $this->campo('hora'),
            'categoria'   => $this->campo('tipo', 'otros'),
            'es_gratuito' => (float) ($this->campo('precio', 1)) === 0.0,
            'enlace'      => $this->campo('url_entradas'),
            'imagen'      => $this->imagen,
            'activo'      => true,
        ]);
        $fake->setAttribute('id', 'ev_' . $this->id);
        $fake->setAttribute('modulo_item_id', $this->id);
        $fake->setAttribute('punto_slug', $this->punto?->slug);
        $fake->setRelation('imagenes', collect());

        return $fake;
    }
}
