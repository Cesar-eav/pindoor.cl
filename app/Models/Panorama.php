<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Panorama extends Model
{
    protected $fillable = [
        'titulo',
        'ubicacion',
        'fecha',
        'hora',
        'imagen',
        'activo',
        'orden',
    ];

    protected $casts = [
        'fecha'  => 'date',
        'activo' => 'boolean',
    ];

    public function scopeActivos($query, $limite = 15)
    {

        
        return $query->where('activo', true)
        ->whereBetween('fecha', [
            Carbon::today(),
            Carbon::today()->addDays($limite),
        ])
        ->orderBy('orden')
        ->orderBy('id');
    }
}
