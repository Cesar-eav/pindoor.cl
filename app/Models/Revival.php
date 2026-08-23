<?php

namespace App\Models;

use App\Models\Concerns\HasPreviewToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Revival extends Model
{
    use HasTranslations, HasPreviewToken;

    public array $translatable = ['titulo', 'contenido'];

    public function getFallbackLocale(?string $locale = null): ?string
    {
        return $locale !== 'es' ? 'es' : null;
    }

    protected $fillable = [
        'titulo',
        'slug',
        'contenido',
        'imagen_portada',
        'autor',
        'video_url',
        'publicado',
        'publicado_en',
    ];

    protected $casts = [
        'publicado'    => 'boolean',
        'publicado_en' => 'datetime',
    ];

    public function imagenes()
    {
        return $this->hasMany(RevivalImagen::class)->orderBy('orden');
    }

    public function scopePublicados($query)
    {
        return $query->where('publicado', true)->orderByDesc('publicado_en');
    }

    public function previewEstaVivo(): bool
    {
        return ! $this->publicado;
    }

    public function previewTipo(): string
    {
        return 'revival';
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
        static::deleting(function (self $revival) {
            if ($revival->imagen_portada) {
                Storage::disk('public')->delete($revival->imagen_portada);
            }
            foreach ($revival->imagenes as $img) {
                Storage::disk('public')->delete($img->ruta);
            }
        });
    }
}
