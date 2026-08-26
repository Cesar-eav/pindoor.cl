<?php

namespace App\Models;

use App\Models\Concerns\HasPreviewToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTranslations, HasPreviewToken;

    public array $translatable = ['titulo', 'resumen', 'contenido'];

    public function getFallbackLocale(?string $locale = null): ?string
    {
        return $locale !== 'es' ? 'es' : null;
    }

    protected $fillable = [
        'titulo',
        'slug',
        'dynamic_block_title',
        'resumen',
        'contenido',
        'imagen_portada',
        'publicado',
        'publicado_en',
        'orden_home',
    ];

    protected $casts = [
        'publicado'    => 'boolean',
        'publicado_en' => 'datetime',
    ];

    public function imagenes()
    {
        return $this->hasMany(PostImagen::class)->orderBy('orden');
    }

    public function scopePublicados($query)
    {
        return $query->where('publicado', true)->orderByDesc('publicado_en');
    }

    /**
     * Orden manual solo para la vista previa del home — /blog sigue usando
     * scopePublicados() (por fecha) sin tocar.
     */
    public function scopeOrdenHome($query)
    {
        return $query->publicados()->reorder()->orderBy('orden_home')->orderByDesc('publicado_en');
    }

    public function previewEstaVivo(): bool
    {
        return ! $this->publicado;
    }

    public function previewTipo(): string
    {
        return 'blog';
    }

    public function lugares()
    {
        return $this->belongsToMany(PuntoInteres::class, 'post_punto_interes')
            ->withPivot('orden')
            ->orderBy('post_punto_interes.orden');
    }

    public function rutas()
    {
        return $this->belongsToMany(Ruta::class, 'post_ruta');
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
            foreach ($post->imagenes as $img) {
                Storage::disk('public')->delete($img->ruta);
            }
        });
    }
}
