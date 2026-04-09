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
        'servicios_incluidos'     => 'array',
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
        'precio_desde',
        'check_in',
        'check_out',
        'tipos_habitacion',
        'servicios_incluidos',
        'politicas',
    ];

    public function esAlojamiento(): bool
    {
        return $this->categoria_id === 11;
    }

    public function esAlimentacion(): bool
    {
        return in_array($this->categoria_id, [2, 10]);
    }

    // Catálogo de servicios de alojamiento disponibles
    public static function catalogoServicios(): array
    {
        return [
            'recepcion_24h'    => ['emoji' => '🔑', 'label' => 'Recepción 24h'],
            'wifi'             => ['emoji' => '📶', 'label' => 'WiFi gratis'],
            'desayuno'         => ['emoji' => '☕', 'label' => 'Desayuno incluido'],
            'desayuno_buffet'  => ['emoji' => '🍳', 'label' => 'Desayuno buffet'],
            'almuerzo'         => ['emoji' => '🥗', 'label' => 'Almuerzo'],
            'cena'             => ['emoji' => '🍽️', 'label' => 'Cena'],
            'bar'              => ['emoji' => '🍷', 'label' => 'Bar'],
            'lavanderia'       => ['emoji' => '👕', 'label' => 'Lavandería'],
            'estacionamiento'  => ['emoji' => '🚗', 'label' => 'Estacionamiento'],
            'tours'            => ['emoji' => '🗺️', 'label' => 'Tours y excursiones'],
            'cocina_comun'     => ['emoji' => '🍲', 'label' => 'Cocina comunitaria'],
            'sala_comun'       => ['emoji' => '🛋️', 'label' => 'Sala común / Lounge'],
            'sala_eventos'     => ['emoji' => '🎭', 'label' => 'Sala de eventos'],
            'cowork'           => ['emoji' => '💻', 'label' => 'Coworking'],
            'spa'              => ['emoji' => '💆', 'label' => 'Spa'],
            'gimnasio'         => ['emoji' => '🏋️', 'label' => 'Gimnasio'],
            'piscina'          => ['emoji' => '🏊', 'label' => 'Piscina'],
            'terraza'          => ['emoji' => '🌅', 'label' => 'Terraza / Rooftop'],
            'vista_mar'        => ['emoji' => '🌊', 'label' => 'Vista al mar/cerro'],
            'mascotas'         => ['emoji' => '🐾', 'label' => 'Pet friendly'],
            'traslado'         => ['emoji' => '🚌', 'label' => 'Traslado aeropuerto'],
        ];
    }

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
