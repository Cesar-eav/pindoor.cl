<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservaGestion extends Model
{
    protected $table = 'reserva_gestiones';

    public $timestamps = false;

    protected $fillable = [
        'ticketera_reserva_id',
        'tipo',
        'estado_anterior',
        'estado_nuevo',
        'horario_anterior_id',
        'fecha_anterior',
        'horario_nuevo_id',
        'fecha_nueva',
        'motivo',
        'admin_id',
    ];

    protected $casts = [
        'fecha_anterior' => 'date',
        'fecha_nueva'    => 'date',
        'created_at'     => 'datetime',
    ];

    public const TIPOS_INFO = [
        'reembolso'      => ['label' => 'Reembolso', 'icon' => '💸'],
        'reagendamiento' => ['label' => 'Reagendamiento', 'icon' => '🔁'],
        'nota'           => ['label' => 'Nota interna', 'icon' => '📝'],
    ];

    public function reserva()
    {
        return $this->belongsTo(ReservaRuta::class, 'ticketera_reserva_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function horarioAnterior()
    {
        return $this->belongsTo(RutaOperadorHorario::class, 'horario_anterior_id');
    }

    public function horarioNuevo()
    {
        return $this->belongsTo(RutaOperadorHorario::class, 'horario_nuevo_id');
    }
}
