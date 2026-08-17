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
        'imagen_portada',
        'mostrar_nombre_en_imagen',
        'descripcion',
        'modulos_defecto',
        'es_cliente',
        'disponible_para_operadores',
        'grupo_id',
        'orden',
        'activa_en_home',
    ];

    protected $casts = [
        'modulos_defecto'             => 'array',
        'es_cliente'                  => 'boolean',
        'mostrar_nombre_en_imagen'    => 'boolean',
        'disponible_para_operadores'  => 'boolean',
        'activa_en_home'              => 'boolean',
    ];

    /**
     * Relación: Una categoría tiene muchos atractivos
     */
    public function puntosInteres()
    {
        return $this->hasMany(PuntoInteres::class);
    }

    public function grupo()
    {
        return $this->belongsTo(CategoriaGrupo::class, 'grupo_id');
    }
}
