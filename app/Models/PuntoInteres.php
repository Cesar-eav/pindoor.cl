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
        'modulos_habilitados'     => 'array',
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
        'modulos_habilitados',
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

    // Catálogo de todos los módulos disponibles para clientes
    public static function catalogoModulos(): array
    {
        return [
            'oferta_del_dia' => ['label' => 'Oferta del día',            'emoji' => '🏷️', 'desc' => 'Promociones o avisos diarios'],
            'menu_del_dia'   => ['label' => 'Menú del día',              'emoji' => '🥘', 'desc' => 'Menú de almuerzo o cena del día'],
            'carta'          => ['label' => 'Carta / Menú permanente',   'emoji' => '🍽️', 'desc' => 'Carta completa del local'],
            'habitaciones'   => ['label' => 'Habitaciones y precios',    'emoji' => '🛏️', 'desc' => 'Tipos de habitación y tarifas'],
            'servicios'      => ['label' => 'Servicios incluidos',       'emoji' => '✨', 'desc' => 'Amenidades del alojamiento'],
            'politicas'      => ['label' => 'Políticas',                 'emoji' => '📋', 'desc' => 'Check-in/out, cancelación, normas'],
        ];
    }

    // Módulos activos por defecto según categoría del punto
    public static function modulosDefault(int $categoriaId): array
    {
        return match(true) {
            in_array($categoriaId, [2, 10]) => ['oferta_del_dia', 'menu_del_dia', 'carta'],
            $categoriaId === 11             => ['oferta_del_dia', 'habitaciones', 'servicios', 'politicas'],
            default                         => ['oferta_del_dia'],
        };
    }

    // Comprueba si un módulo está habilitado para este punto
    public function moduloActivo(string $modulo): bool
    {
        return in_array($modulo, $this->modulos_habilitados ?? []);
    }

    public function esAlojamiento(): bool
    {
        return $this->categoria_id === 11;
    }

    public function esAlimentacion(): bool
    {
        return in_array($this->categoria_id, [2, 10]);
    }

    // Catálogo de servicios de alojamiento agrupados por categoría
    public static function catalogoServicios(): array
    {
        return [
            'Esenciales' => [
                'recepcion_24h'   => ['emoji' => '🔑', 'label' => 'Recepción 24h'],
                'wifi'            => ['emoji' => '📶', 'label' => 'WiFi gratis'],
                'lavanderia'      => ['emoji' => '👕', 'label' => 'Lavandería'],
                'estacionamiento' => ['emoji' => '🚗', 'label' => 'Estacionamiento'],
            ],
            'Alimentación & Bar' => [
                'desayuno'        => ['emoji' => '☕', 'label' => 'Desayuno incluido'],
                'desayuno_buffet' => ['emoji' => '🍳', 'label' => 'Desayuno buffet'],
                'almuerzo'        => ['emoji' => '🥗', 'label' => 'Almuerzo'],
                'cena'            => ['emoji' => '🍽️', 'label' => 'Cena'],
                'bar'             => ['emoji' => '🍷', 'label' => 'Bar'],
            ],
            'Bienestar' => [
                'spa'             => ['emoji' => '💆', 'label' => 'Spa'],
                'gimnasio'        => ['emoji' => '🏋️', 'label' => 'Gimnasio'],
                'piscina'         => ['emoji' => '🏊', 'label' => 'Piscina'],
            ],
            'Espacios Sociales & Trabajo' => [
                'cocina_comun'    => ['emoji' => '🍲', 'label' => 'Cocina comunitaria'],
                'sala_comun'      => ['emoji' => '🛋️', 'label' => 'Sala común / Lounge'],
                'cowork'          => ['emoji' => '💻', 'label' => 'Coworking'],
                'sala_eventos'    => ['emoji' => '🎭', 'label' => 'Sala de eventos'],
            ],
            'Experiencias & Entorno' => [
                'tours'           => ['emoji' => '🗺️', 'label' => 'Tours y excursiones'],
                'traslado'        => ['emoji' => '🚌', 'label' => 'Traslado aeropuerto'],
                'terraza'         => ['emoji' => '🌅', 'label' => 'Terraza / Rooftop'],
                'vista_mar'       => ['emoji' => '🌊', 'label' => 'Vista al mar/cerro'],
                'mascotas'        => ['emoji' => '🐾', 'label' => 'Pet friendly'],
            ],
        ];
    }

    // Versión plana del catálogo para lookups por slug
    public static function catalogoServiciosPlano(): array
    {
        return collect(self::catalogoServicios())->flatMap(fn($s) => $s)->all();
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
