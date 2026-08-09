<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OperadorTuristico extends Model
{
    protected $table = 'operadores_turisticos';

    protected $fillable = [
        'user_id', 'nombre', 'slug', 'descripcion',
        'imagen_perfil', 'ciudad',
        'email_contacto', 'telefono',
        'enlace_web', 'enlace_instagram', 'enlace_facebook', 'enlace_whatsapp',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function puntos()
    {
        return $this->belongsToMany(PuntoInteres::class, 'operador_punto_interes', 'operador_turistico_id', 'punto_interes_id');
    }

    public function rutas()
    {
        return $this->belongsToMany(Ruta::class, 'ruta_operador_turistico');
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
}
