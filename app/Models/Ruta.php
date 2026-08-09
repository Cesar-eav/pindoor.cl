<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Ruta extends Model
{
    use HasTranslations;

    public array $translatable = ['titulo', 'descripcion'];

    public function getFallbackLocale(?string $locale = null): ?string
    {
        return $locale !== 'es' ? 'es' : null;
    }

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'imagen_portada',
        'publicado',
        'publicado_en',
    ];

    protected $casts = [
        'publicado'    => 'boolean',
        'publicado_en' => 'datetime',
    ];

    public function scopePublicadas($query)
    {
        return $query->where('publicado', true)->orderByDesc('publicado_en');
    }

    public function puntos()
    {
        return $this->belongsToMany(PuntoInteres::class, 'ruta_punto_interes')
            ->withPivot('orden')
            ->orderBy('ruta_punto_interes.orden');
    }

    public function operadores()
    {
        return $this->belongsToMany(OperadorTuristico::class, 'ruta_operador_turistico');
    }

    public function getImagenPortadaUrlAttribute(): ?string
    {
        return $this->imagen_portada ? asset('storage/' . $this->imagen_portada) : null;
    }

    public static function generarSlug(string $titulo, ?int $exceptId = null): string
    {
        $base = Str::slug($titulo);
        $slug = $base;
        $i    = 1;

        while (
            static::where('slug', $slug)
                  ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                  ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    protected static function booted(): void
    {
        static::deleting(function (self $ruta) {
            if ($ruta->imagen_portada) {
                Storage::disk('public')->delete($ruta->imagen_portada);
            }
        });
    }
}
