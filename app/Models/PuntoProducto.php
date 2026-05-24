<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PuntoProducto extends Model
{
    protected $table = 'punto_productos';

    protected $fillable = [
        'punto_interes_id',
        'nombre',
        'precio',
        'descripcion',
        'imagen',
        'orden',
    ];

    public function punto()
    {
        return $this->belongsTo(PuntoInteres::class, 'punto_interes_id');
    }

    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : null;
    }

    protected static function booted(): void
    {
        static::deleting(function (self $producto) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
        });
    }
}
