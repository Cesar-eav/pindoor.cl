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
        'conferencia' => ['label' => 'Conferencia',      'emoji' => '🎓'],
        'exposicion'  => ['label' => 'Exposición',      'emoji' => '🧐'],
        'otros'       => ['label' => 'Otros',      'emoji' => '🫘'],
        

    ];

    const DIAS = [
        1 => 'Lun', 2 => 'Mar', 3 => 'Mié',
        4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom',
    ];

    protected $fillable = [
        'titulo',
        'ubicacion',
        'fecha',
        'fecha_fin',
        'dias_semana',
        'hora',
        'categoria',
        'es_gratuito',
        'enlace',
        'imagen',
        'activo',
        'orden',
    ];

    protected $casts = [
        'fecha'       => 'date',
        'fecha_fin'   => 'date',
        'dias_semana' => 'array',
        'activo'      => 'boolean',
        'es_gratuito' => 'boolean',
    ];

    public function imagenes()
    {
        return $this->hasMany(PanoramaImagen::class)->orderBy('orden');
    }

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
