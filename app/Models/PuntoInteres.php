<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoInteres extends Model
{
    use HasFactory;

    protected $table = 'puntosinteres';

    public function imagenes()
        {
            return $this->hasMany(ImagenPunto::class, 'punto_interes_id');
        }
}
