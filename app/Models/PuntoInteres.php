<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoInteres extends Model
{
    use HasFactory;

    protected $table = 'puntosinteres';

    protected $casts = [
        'tags'               => 'array',
        'modulos_habilitados'=> 'array',
        'oferta_expira_at'   => 'datetime',
        'oferta_activa'      => 'boolean',
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
        'descripcion_busqueda',
        'imagen_perfil',
    ];

    // ═══════════════════════════════════════════════════════════════════════════
    // CATÁLOGOS DE MÓDULOS
    // ═══════════════════════════════════════════════════════════════════════════

    /** Catálogo completo de módulos disponibles para clientes. */
    public static function catalogoModulos(): array
    {
        return [
            'oferta_del_dia' => ['label' => 'Oferta del día',          'emoji' => '🏷️', 'desc' => 'Promociones o avisos diarios'],
            'menu_del_dia'   => ['label' => 'Menú del día',            'emoji' => '🥘', 'desc' => 'Menú de almuerzo o cena del día'],
            'carta'          => ['label' => 'Carta / Menú permanente', 'emoji' => '🍽️', 'desc' => 'Carta completa del local'],
            'habitaciones'   => ['label' => 'Habitaciones y precios',  'emoji' => '🛏️', 'desc' => 'Tipos de habitación y tarifas'],
            'servicios'      => ['label' => 'Servicios incluidos',     'emoji' => '✨', 'desc' => 'Amenidades del alojamiento'],
            'politicas'      => ['label' => 'Políticas',               'emoji' => '📋', 'desc' => 'Check-in/out, cancelación, normas'],
            'entradas'       => ['label' => 'Entradas y tarifas',      'emoji' => '🎟️', 'desc' => 'Precios de entrada al museo'],
            'exposiciones'   => ['label' => 'Exposiciones',            'emoji' => '🖼️', 'desc' => 'Colecciones permanentes y temporales'],
            'agenda'         => ['label' => 'Agenda cultural',         'emoji' => '📅', 'desc' => 'Programación de eventos y espectáculos'],
        ];
    }

    /** Módulos activos por defecto según la categoría del punto. */
    public static function modulosDefecto(int $categoriaId): array
    {
        return match (true) {
            in_array($categoriaId, [2, 8, 10]) => ['oferta_del_dia', 'menu_del_dia', 'carta'],
            $categoriaId === 11                 => ['oferta_del_dia', 'habitaciones', 'servicios', 'politicas'],
            $categoriaId === 7                  => ['entradas', 'exposiciones', 'oferta_del_dia'],
            $categoriaId === 5                  => ['agenda', 'oferta_del_dia'],
            default                             => ['oferta_del_dia'],
        };
    }

    /** Alias en inglés para compatibilidad con llamadas existentes en AdminController. */
    public static function modulosDefault(int $categoriaId): array
    {
        return static::modulosDefecto($categoriaId);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // RELACIONES
    // ═══════════════════════════════════════════════════════════════════════════

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @deprecated Usa usuario() */
    public function user()
    {
        return $this->usuario();
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenPunto::class, 'punto_interes_id');
    }

    public function imagenPrincipal()
    {
        return $this->hasOne(ImagenPunto::class, 'punto_interes_id')
                    ->where('es_principal', true);
    }

    /** Datos singleton de módulos (uno por módulo). */
    public function moduloDatos()
    {
        return $this->hasMany(ModuloDato::class, 'punto_interes_id');
    }

    /** Ítems de lista de módulos (múltiples por módulo). */
    public function moduloItems()
    {
        return $this->hasMany(ModuloItem::class, 'punto_interes_id');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // HELPERS DE MÓDULOS
    // ═══════════════════════════════════════════════════════════════════════════

    /** Comprueba si un módulo está habilitado para este punto. */
    public function moduloActivo(string $modulo): bool
    {
        return in_array($modulo, $this->modulos_habilitados ?? []);
    }

    /**
     * Devuelve el array 'datos' del módulo singleton indicado.
     * Si la relación ya está cargada no hace consulta extra.
     */
    public function dato(string $modulo): array
    {
        $this->loadMissing('moduloDatos');
        return $this->moduloDatos->firstWhere('modulo', $modulo)?->datos ?? [];
    }

    /**
     * Devuelve el registro ModuloDato completo de un módulo (para acceder a actualizado_en, etc.).
     */
    public function registroDato(string $modulo): ?ModuloDato
    {
        $this->loadMissing('moduloDatos');
        return $this->moduloDatos->firstWhere('modulo', $modulo);
    }

    /**
     * Devuelve la colección de ítems activos de un módulo, ordenados por 'orden'.
     * Si la relación ya está cargada no hace consulta extra.
     */
    public function items(string $modulo)
    {
        $this->loadMissing('moduloItems');
        return $this->moduloItems
            ->where('modulo', $modulo)
            ->where('activo', true)
            ->sortBy('orden')
            ->values();
    }

    /**
     * Devuelve los ítems de 'eventos' con fecha futura, ordenados por fecha y hora.
     */
    public function eventosProximos()
    {
        $this->loadMissing('moduloItems');
        return $this->moduloItems
            ->where('modulo', 'eventos')
            ->where('activo', true)
            ->filter(fn($i) => $i->fecha && $i->fecha->greaterThanOrEqualTo(today()))
            ->sortBy([['fecha', 'asc'], ['datos.hora', 'asc']])
            ->values();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // HELPERS DE TIPO DE NEGOCIO
    // ═══════════════════════════════════════════════════════════════════════════

    public function esAlojamiento(): bool
    {
        return $this->categoria_id === 11;
    }

    public function esAlimentacion(): bool
    {
        return in_array($this->categoria_id, [2, 8, 10]);
    }

    public function esMuseo(): bool
    {
        return $this->categoria_id === 7;
    }

    public function esCultura(): bool
    {
        return $this->categoria_id === 5;
    }

    public function esComercio(): bool
    {
        return $this->categoria_id === 12;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // HELPERS DE CONTENIDO
    // ═══════════════════════════════════════════════════════════════════════════

    public function tieneOfertaActiva(): bool
    {
        return $this->es_cliente
            && $this->oferta_activa
            && $this->oferta_del_dia
            && ($this->oferta_expira_at === null || $this->oferta_expira_at->isFuture());
    }

    public function tieneCarta(): bool
    {
        $carta = $this->dato('carta');
        return $this->es_cliente
            && $this->moduloActivo('carta')
            && (!empty($carta['texto']) || !empty($carta['pdf_ruta']));
    }

    public function tieneMenu(): bool
    {
        return $this->es_cliente
            && $this->moduloActivo('menu_del_dia')
            && !empty($this->dato('menu_del_dia')['texto'] ?? '');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // CATÁLOGO DE SERVICIOS DE ALOJAMIENTO
    // ═══════════════════════════════════════════════════════════════════════════

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

    public static function catalogoServiciosPlano(): array
    {
        return collect(self::catalogoServicios())->flatMap(fn($s) => $s)->all();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // SCOPES
    // ═══════════════════════════════════════════════════════════════════════════

    public function scopeClientes($query)
    {
        return $query->where('es_cliente', true);
    }

    public function scopeAtractivos($query)
    {
        return $query->where('es_cliente', false);
    }
}
