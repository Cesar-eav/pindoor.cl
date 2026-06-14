<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    public array $translatable = ['titulo', 'resumen', 'contenido'];

    protected $fillable = [
        'titulo',
        'slug',
        'resumen',
        'contenido',
        'imagen_portada',
        'imagenes',
        'publicado',
        'publicado_en',
    ];

    protected $casts = [
        'publicado'    => 'boolean',
        'publicado_en' => 'datetime',
        'imagenes'     => 'array',
    ];

    public function scopePublicados($query)
    {
        return $query->where('publicado', true)->orderByDesc('publicado_en');
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
        static::deleting(function (self $post) {
            if ($post->imagen_portada) {
                Storage::disk('public')->delete($post->imagen_portada);
            }
        });
    }
}
