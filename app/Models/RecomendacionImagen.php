<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecomendacionImagen extends Model
{
    protected $table = 'recomendacion_imagenes';

    protected $fillable = ['recomendacion_id', 'ruta', 'orden', 'posicion'];

    public function recomendacion()
    {
        return $this->belongsTo(Recomendacion::class);
    }
}
