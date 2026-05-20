<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtistaImagen extends Model
{
    protected $table    = 'artista_imagenes';
    protected $fillable = ['artista_id', 'ruta', 'orden'];

    public function artista()
    {
        return $this->belongsTo(Artista::class);
    }
}
