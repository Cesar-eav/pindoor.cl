<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoFicha extends Model
{
    protected $table = 'eventos_ficha';

    protected $fillable = [
        'punto_interes_id',
        'tipo',
    ];

    public function puntoInteres()
    {
        return $this->belongsTo(PuntoInteres::class);
    }
}
