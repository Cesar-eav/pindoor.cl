<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenPunto extends Model
{
    use HasFactory;

        protected $table = 'imagenes_punto';

        protected $guarded = [];

        public function lugar()
        {
            return $this->belongsTo(PuntoInteres::class, 'punto_interes_id');
        }


}
