<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'tipo',
        'icono',
        'descripcion',
    ];

    /**
     * Relación: Una categoría tiene muchos atractivos
     */
    public function puntosInteres()
    {
        return $this->hasMany(PuntoInteres::class);
    }
}
