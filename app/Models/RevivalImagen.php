<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevivalImagen extends Model
{
    protected $table = 'revival_imagenes';

    protected $fillable = ['revival_id', 'ruta', 'orden', 'posicion'];

    public function revival()
    {
        return $this->belongsTo(Revival::class);
    }
}
