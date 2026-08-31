<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RutaOperadorHorario extends Model
{
    protected $fillable = [
        'ruta_operador_turistico_id',
        'tipo',
        'dias_semana',
        'fecha',
        'hora',
        'cupo_maximo',
        'activo',
    ];

    protected $casts = [
        'dias_semana' => 'array',
        'fecha'       => 'date',
        'activo'      => 'boolean',
    ];

    public function rutaOperador()
    {
        return $this->belongsTo(RutaOperador::class, 'ruta_operador_turistico_id');
    }

    public function reservas()
    {
        return $this->hasMany(ReservaRuta::class);
    }

    public function aplicaEnFecha(Carbon $fecha): bool
    {
        if ($fecha->isPast() && !$fecha->isToday()) {
            return false;
        }

        if ($this->tipo === 'fecha') {
            return $this->fecha->isSameDay($fecha);
        }

        return in_array($fecha->isoWeekday(), $this->dias_semana ?? [], true);
    }

    public function cupoOcupado(Carbon $fecha): int
    {
        return $this->reservas()
            ->whereDate('fecha_visita', $fecha)
            ->where(function ($q) {
                $q->where('estado', 'pagada')
                  ->orWhere(function ($q2) {
                      $q2->where('estado', 'pendiente')->where('expira_en', '>', now());
                  });
            })
            ->selectRaw('COALESCE(SUM(cantidad_adultos + cantidad_ninos), 0) as total')
            ->value('total');
    }

    public function cupoDisponible(Carbon $fecha): int
    {
        return max(0, $this->cupo_maximo - $this->cupoOcupado($fecha));
    }

    public function resumenTexto(): string
    {
        $hora = substr($this->hora, 0, 5);

        if ($this->tipo === 'fecha') {
            return $this->fecha->translatedFormat('d \d\e F, Y') . " · {$hora} hrs";
        }

        $nombres = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];
        $dias = collect($this->dias_semana ?? [])->sort()->map(fn ($d) => $nombres[$d] ?? '')->implode(', ');

        return "{$dias} · {$hora} hrs";
    }
}
