<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Artista extends Model
{
    const DISCIPLINAS = [
        'musica'          => ['label' => 'Música',          'emoji' => '🎵'],
        'teatro'          => ['label' => 'Teatro',          'emoji' => '🎭'],
        'danza'           => ['label' => 'Danza',           'emoji' => '💃'],
        'artes_visuales'  => ['label' => 'Artes visuales',  'emoji' => '🎨'],
        'fotografia'      => ['label' => 'Fotografía',      'emoji' => '📷'],
        'literatura'      => ['label' => 'Literatura',      'emoji' => '📚'],
        'circo'           => ['label' => 'Circo',           'emoji' => '🎪'],
        'audiovisual'     => ['label' => 'Audiovisual',     'emoji' => '🎬'],
        'ceramica'        => ['label' => 'Cerámica',        'emoji' => '🏺'],
        'muralismo'       => ['label' => 'Muralismo',       'emoji' => '🖼️'],
        'otro'            => ['label' => 'Otro',            'emoji' => '✨'],
    ];

    protected $fillable = [
        'user_id', 'nombre', 'slug', 'disciplina', 'descripcion',
        'imagen_perfil', 'ciudad',
        'email_contacto', 'telefono',
        'enlace_web', 'enlace_instagram', 'enlace_facebook',
        'enlace_spotify', 'enlace_youtube',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ArtistaImagen::class)->orderBy('orden');
    }

    public static function slugUnico(string $nombre): string
    {
        $base = Str::slug($nombre);
        $slug = $base;
        $i    = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function disciplinaLabel(): string
    {
        return self::DISCIPLINAS[$this->disciplina]['emoji'] . ' '
             . (self::DISCIPLINAS[$this->disciplina]['label'] ?? $this->disciplina);
    }
}
