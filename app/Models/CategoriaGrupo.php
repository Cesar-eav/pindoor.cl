<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaGrupo extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'icono',
        'orden',
    ];

    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'grupo_id')->orderBy('nombre');
    }
}
