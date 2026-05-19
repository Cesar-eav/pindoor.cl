<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanoramaImagen extends Model
{
    protected $table = 'panorama_imagenes';

    protected $fillable = ['panorama_id', 'ruta', 'orden'];

    public function panorama()
    {
        return $this->belongsTo(Panorama::class);
    }
}
