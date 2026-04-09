<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoInteres extends Model
{
    use HasFactory;

    protected $table = 'puntosinteres';

    protected $casts = [
        'tags'                    => 'array',
        'menu_del_dia_updated_at' => 'datetime',
        'carta_updated_at'        => 'datetime',
        'oferta_expira_at'        => 'datetime',
        'oferta_activa'           => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'categoria_id',
        'title',
        'slug',
        'sector',
        'description',
        'direccion',
        'lat',
        'lng',
        'video_url',
        'enlace',
        'horario',
        'autor',
        'tags',
        'activo',
        'eliminado',
        'es_cliente',
        'oferta_del_dia',
        'oferta_activa',
        'oferta_expira_at',
        'menu_del_dia',
        'descripcion_busqueda',
        'imagen_perfil',
        'carta',
        'carta_pdf',
        'menu_del_dia_updated_at',
        'carta_updated_at',
    ];

    public function tieneOfertaActiva(): bool
    {
        return $this->es_cliente
            && $this->oferta_activa
            && $this->oferta_del_dia
            && ($this->oferta_expira_at === null || $this->oferta_expira_at->isFuture());
    }

    public function tieneCarta(): bool
    {
        return $this->es_cliente
            && $this->categoria?->tipo === 'alimentacion'
            && ($this->carta || $this->carta_pdf);
    }

    public function scopeClientes($query)
    {
        return $query->where('es_cliente', true);
    }

    public function scopeAtractivos($query)
    {
        return $query->where('es_cliente', false);
    }

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
