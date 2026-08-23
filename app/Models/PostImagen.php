<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostImagen extends Model
{
    protected $table = 'post_imagenes';

    protected $fillable = ['post_id', 'ruta', 'orden', 'posicion'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
