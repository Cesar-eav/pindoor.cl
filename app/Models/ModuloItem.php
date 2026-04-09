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

    // ─── Catálogos estáticos ────────────────────────────────────────────────────

    public static function catalogoTiposEvento(): array
    {
        return [
            'teatro'      => ['emoji' => '🎭', 'label' => 'Teatro'],
            'cine'        => ['emoji' => '🎬', 'label' => 'Cine'],
            'concierto'   => ['emoji' => '🎵', 'label' => 'Concierto'],
            'exposicion'  => ['emoji' => '🖼️', 'label' => 'Exposición'],
            'taller'      => ['emoji' => '🎨', 'label' => 'Taller'],
            'danza'       => ['emoji' => '💃', 'label' => 'Danza'],
            'conferencia' => ['emoji' => '🎤', 'label' => 'Conferencia'],
            'otro'        => ['emoji' => '📌', 'label' => 'Otro'],
        ];
    }

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
        $tipo = $this->datos['tipo'] ?? 'otro';
        return static::catalogoTiposEvento()[$tipo] ?? ['emoji' => '📌', 'label' => $tipo];
    }
}
