<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadCliente extends Model
{
    protected $table = 'actividades_clientes';

    protected $fillable = [
        'user_id',
        'punto_interes_id',
        'tipo',
        'detalle',
    ];

    public static function registrar(PuntoInteres $punto, string $tipo, ?string $detalle = null): void
    {
        static::create([
            'user_id'          => auth()->id(),
            'punto_interes_id' => $punto->id,
            'tipo'             => $tipo,
            'detalle'          => $detalle,
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function puntoInteres()
    {
        return $this->belongsTo(PuntoInteres::class, 'punto_interes_id');
    }
}
