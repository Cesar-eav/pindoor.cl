<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenPunto extends Model
{
    use HasFactory;

        protected $table = 'imagenes_punto';

        protected $guarded = [];

        protected $fillable = [
        'punto_interes_id',
        'ruta',
        'es_principal',
        'orden',
    ];

        public function puntoInteres()
        {
            return $this->belongsTo(PuntoInteres::class, 'punto_interes_id');
        }


}
