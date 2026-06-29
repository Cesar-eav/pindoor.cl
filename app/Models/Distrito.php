<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $fillable = ['nombre', 'color', 'coordenadas', 'activo', 'orden'];

    protected $casts = [
        'coordenadas' => 'array',
        'activo'      => 'boolean',
    ];
}
