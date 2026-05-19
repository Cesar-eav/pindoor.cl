<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Panorama extends Model
{
    const CATEGORIAS = [
        'musica'      => ['label' => 'Música',      'emoji' => '🎵'],
        'teatro'      => ['label' => 'Teatro',      'emoji' => '🎭'],
        'danza'       => ['label' => 'Danza',       'emoji' => '💃'],
        'arte'        => ['label' => 'Arte',        'emoji' => '🎨'],
        'cine'        => ['label' => 'Cine',        'emoji' => '🎬'],
        'gastronomia' => ['label' => 'Gastronomía', 'emoji' => '🍽️'],
        'feria'       => ['label' => 'Feria',       'emoji' => '🛍️'],
        'tour'        => ['label' => 'Tour',        'emoji' => '🗺️'],
        'infantil'    => ['label' => 'Infantil',    'emoji' => '🧸'],
        'taller'      => ['label' => 'Taller',      'emoji' => '🛠️'],

    ];

    protected $fillable = [
        'titulo',
        'ubicacion',
        'fecha',
        'fecha_fin',
        'hora',
        'categoria',
        'es_gratuito',
        'imagen',
        'activo',
        'orden',
    ];

    protected $casts = [
        'fecha'       => 'date',
        'fecha_fin'   => 'date',
        'activo'      => 'boolean',
        'es_gratuito' => 'boolean',
    ];

    public function scopeActivos($query, $limite = 15)
    {
        $hoy   = Carbon::today();
        $hasta = Carbon::today()->addDays($limite);

        return $query->where('activo', true)
            ->where('fecha', '<=', $hasta)
            ->where(function ($q) use ($hoy) {
                // Eventos sin fecha_fin: que inicien desde hoy
                // Eventos con fecha_fin: que aún no hayan terminado
                $q->whereNull('fecha_fin')->where('fecha', '>=', $hoy)
                  ->orWhere('fecha_fin', '>=', $hoy);
            });
    }
}
