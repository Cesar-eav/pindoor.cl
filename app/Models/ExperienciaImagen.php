<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienciaImagen extends Model
{
    protected $table = 'experiencia_imagenes';

    protected $fillable = ['experiencia_id', 'ruta', 'orden'];

    public function experiencia()
    {
        return $this->belongsTo(Experiencia::class);
    }
}
