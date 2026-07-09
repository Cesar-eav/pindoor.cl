<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Categoria extends Model
{
    use HasTranslations;

    public array $translatable = ['nombre', 'descripcion'];

    protected $fillable = [
        'nombre',
        'slug',
        'tipo',
        'icono',
        'descripcion',
        'modulos_defecto',
        'es_cliente',
    ];

    protected $casts = [
        'modulos_defecto' => 'array',
        'es_cliente'      => 'boolean',
    ];

    /**
     * Relación: Una categoría tiene muchos atractivos
     */
    public function puntosInteres()
    {
        return $this->hasMany(PuntoInteres::class);
    }
}
