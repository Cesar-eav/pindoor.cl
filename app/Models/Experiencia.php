<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Experiencia extends Model
{
    const CATEGORIAS = [
        'danza'       => ['label' => 'Danza',       'emoji' => '💃'],
        'musica'      => ['label' => 'Música',       'emoji' => '🎵'],
        'gastronomia' => ['label' => 'Gastronomía',  'emoji' => '🍽️'],
        'arte'        => ['label' => 'Arte',         'emoji' => '🎨'],
        'aventura'    => ['label' => 'Aventura',     'emoji' => '🏔️'],
        'cultural'    => ['label' => 'Cultural',     'emoji' => '🏛️'],
        'deportes'    => ['label' => 'Deportes',     'emoji' => '⚽'],
        'bienestar'   => ['label' => 'Bienestar',    'emoji' => '🧘'],
        'idiomas'     => ['label' => 'Idiomas',      'emoji' => '🗣️'],
        'taller'      => ['label' => 'Taller',       'emoji' => '🛠️'],
        'otros'       => ['label' => 'Otros',        'emoji' => '✨'],
    ];

    const NIVELES = [
        'todos'         => 'Todos los niveles',
        'principiante'  => 'Principiante',
        'intermedio'    => 'Intermedio',
        'avanzado'      => 'Avanzado',
    ];

    const DIAS = [
        1 => 'Lun', 2 => 'Mar', 3 => 'Mié',
        4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom',
    ];

    protected $fillable = [
        'titulo',
        'proveedor',
        'descripcion',
        'ubicacion',
        'categoria',
        'es_gratuito',
        'precio',
        'duracion',
        'capacidad',
        'nivel',
        'dias_semana',
        'hora',
        'enlace',
        'whatsapp',
        'imagen',
        'activo',
        'orden',
    ];

    protected $casts = [
        'dias_semana' => 'array',
        'activo'      => 'boolean',
        'es_gratuito' => 'boolean',
    ];

    public function imagenes()
    {
        return $this->hasMany(ExperienciaImagen::class)->orderBy('orden');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true)->orderBy('orden')->orderBy('titulo');
    }

    public function getPrecioFormateadoAttribute(): ?string
    {
        if ($this->es_gratuito) return 'Gratis';
        if ($this->precio)     return '$' . number_format($this->precio, 0, ',', '.');
        return null;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (!$this->whatsapp) return null;
        $numero = preg_replace('/\D/', '', $this->whatsapp);
        return 'https://wa.me/' . $numero;
    }

    public function getDiasSemanaLabelAttribute(): string
    {
        if (empty($this->dias_semana)) return '';
        return implode(' · ', array_map(fn($d) => self::DIAS[$d] ?? $d, $this->dias_semana));
    }

    protected static function booted(): void
    {
        static::deleting(function (self $experiencia) {
            if ($experiencia->imagen) {
                Storage::disk('public')->delete($experiencia->imagen);
            }
        });
    }
}
