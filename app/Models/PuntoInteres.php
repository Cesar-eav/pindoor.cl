<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoInteres extends Model
{
    use HasFactory;

    protected $table = 'puntosinteres';

    protected $casts = [
        'tags' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'categoria_id',
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

    public function categoria(){
        return $this->belongsTo(Categoria::class);        
        }


    public function imagenPrincipal()
    {
        return $this->hasOne(ImagenPunto::class, 'punto_interes_id')
                    ->where('es_principal', true);
    }

    }
