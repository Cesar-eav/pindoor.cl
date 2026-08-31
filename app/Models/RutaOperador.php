<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RutaOperador extends Pivot
{
    protected $table = 'ruta_operador_turistico';

    public $incrementing = true;

    protected $fillable = [
        'ruta_id',
        'operador_turistico_id',
        'ticketing_activo',
        'precio_individual',
        'precio_grupo_adulto',
        'precio_nino',
        'edad_maxima_nino',
        'notas_operador',
    ];

    protected $casts = [
        'ticketing_activo' => 'boolean',
    ];

    public function ruta()
    {
        return $this->belongsTo(Ruta::class);
    }

    public function operador()
    {
        return $this->belongsTo(OperadorTuristico::class, 'operador_turistico_id');
    }

    // OJO: RutaOperador extiende Pivot, cuyo trait AsPivot sobreescribe
    // getForeignKey() para su propio bookkeeping interno — la convención
    // automática de Eloquent para hasMany/hasManyThrough no sirve aquí,
    // por eso la foreign key va explícita en ambas relaciones.

    public function horarios()
    {
        return $this->hasMany(RutaOperadorHorario::class, 'ruta_operador_turistico_id');
    }

    public function horariosActivos()
    {
        return $this->horarios()->where('activo', true);
    }

    public function reservas()
    {
        return $this->hasManyThrough(
            ReservaRuta::class,
            RutaOperadorHorario::class,
            'ruta_operador_turistico_id',
            'ruta_operador_horario_id'
        );
    }

    public function bloqueos()
    {
        return $this->hasMany(RutaOperadorBloqueo::class, 'ruta_operador_turistico_id');
    }

    public function fechaBloqueada(Carbon $fecha): bool
    {
        return $this->bloqueos->contains(fn (RutaOperadorBloqueo $b) => $b->fecha->isSameDay($fecha));
    }

    public function calcularPrecio(int $adultos, int $ninos): int
    {
        if ($adultos === 1 && $ninos === 0) {
            return $this->precio_individual;
        }

        return $adultos * $this->precio_grupo_adulto + $ninos * $this->precio_nino;
    }

    public function getPrecioFormateadoAttribute(): string
    {
        return '$' . number_format($this->precio_individual, 0, ',', '.');
    }
}
