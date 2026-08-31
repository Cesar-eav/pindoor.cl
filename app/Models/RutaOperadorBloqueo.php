<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaOperadorBloqueo extends Model
{
    protected $fillable = [
        'ruta_operador_turistico_id',
        'fecha',
        'motivo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function rutaOperador()
    {
        return $this->belongsTo(RutaOperador::class, 'ruta_operador_turistico_id');
    }
}
