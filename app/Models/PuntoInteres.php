<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoInteres extends Model
{
    use HasFactory;

    protected $table = 'puntosinteres';

    protected $fillable = [
        'user_id',      
        'title',
        'slug',
        'category',
        'sector',
        'description',
        'direccion',
        'lat',
        'lng',
        'video_url',
        'horario',
        'autor',
        'tags',
        'activo',
        'eliminado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function imagenes()
        {
            return $this->hasMany(ImagenPunto::class, 'punto_interes_id');
        }
}
