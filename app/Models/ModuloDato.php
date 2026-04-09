<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Almacena el contenido singleton de un módulo para un punto de interés.
 * Un registro por punto+módulo (restricción UNIQUE en BD).
 *
 * Módulos soportados: 'menu_del_dia', 'carta', 'alojamiento', …
 */
class ModuloDato extends Model
{
    protected $table = 'punto_modulo_datos';

    protected $fillable = [
        'punto_interes_id',
        'modulo',
        'datos',
        'actualizado_en',
    ];

    protected $casts = [
        'datos'         => 'array',
        'actualizado_en'=> 'datetime',
    ];

    // ─── Relaciones ────────────────────────────────────────────────────────────

    public function punto()
    {
        return $this->belongsTo(PuntoInteres::class, 'punto_interes_id');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Devuelve un campo del JSON de datos, o $defecto si no existe.
     */
    public function campo(string $clave, mixed $defecto = null): mixed
    {
        return $this->datos[$clave] ?? $defecto;
    }
}
