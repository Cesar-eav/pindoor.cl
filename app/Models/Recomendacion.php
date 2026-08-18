<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Recomendacion extends Model
{
    use HasTranslations;

    public array $translatable = ['titulo', 'resumen', 'contenido'];

    public function getFallbackLocale(?string $locale = null): ?string
    {
        return $locale !== 'es' ? 'es' : null;
    }

    protected $table = 'recomendaciones';

    const PLANES = [
        'basica'     => ['label' => 'Nota Destacada',        'emoji' => '🥉'],
        'intermedia' => ['label' => 'Reseña de Experiencia', 'emoji' => '🥈'],
        'premium'    => ['label' => 'Cobertura Premium',     'emoji' => '🥇'],
    ];

    protected $fillable = [
        'titulo',
        'resumen',
        'slug',
        'punto_interes_id',
        'plan',
        'negocio',
        'rubro',
        'direccion',
        'contenido',
        'video_url',
        'video_youtube',
        'video_en_cabecera',
        'whatsapp',
        'enlace',
        'autor',
        'imagen_portada',
        'destacado_portada',
        'destacado_hasta',
        'publicado',
        'publicado_en',
        'activo',
        'orden',
    ];

    protected $casts = [
        'destacado_portada' => 'boolean',
        'destacado_hasta'   => 'date',
        'publicado'         => 'boolean',
        'publicado_en'      => 'datetime',
        'activo'            => 'boolean',
        'video_en_cabecera' => 'boolean',
    ];

    public function imagenes()
    {
        return $this->hasMany(RecomendacionImagen::class)->orderBy('orden');
    }

    public function puntoInteres()
    {
        return $this->belongsTo(PuntoInteres::class);
    }

    public function scopePublicadas($query)
    {
        return $query->where('publicado', true)->where('activo', true);
    }

    public function scopeDestacadasEnPortada($query)
    {
        return $query->where('destacado_portada', true)
                     ->where(fn ($q) => $q->whereNull('destacado_hasta')->orWhereDate('destacado_hasta', '>=', now()));
    }

    public function getPlanInfoAttribute(): array
    {
        return self::PLANES[$this->plan] ?? ['label' => $this->plan, 'emoji' => '📰'];
    }

    public function getVideoYoutubeIdAttribute(): ?string
    {
        if (!$this->video_youtube) return null;
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->video_youtube, $m)) {
            return $m[1];
        }
        return null;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (!$this->whatsapp) return null;
        $numero = preg_replace('/\D/', '', $this->whatsapp);
        return 'https://wa.me/' . $numero;
    }

    public static function generarSlug(string $titulo, ?int $exceptId = null): string
    {
        $base = Str::slug($titulo);
        $slug = $base;
        $i    = 1;

        while (
            static::where('slug', $slug)
                  ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
                  ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    protected static function booted(): void
    {
        static::deleting(function (self $recomendacion) {
            if ($recomendacion->imagen_portada) {
                Storage::disk('public')->delete($recomendacion->imagen_portada);
            }
            foreach ($recomendacion->imagenes as $img) {
                Storage::disk('public')->delete($img->ruta);
            }
        });
    }
}
