<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PuntoInteres extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['title', 'description'];

    protected $table = 'puntosinteres';

    protected $casts = [
        'modulos_habilitados'=> 'array',
        'oferta_expira_at'   => 'datetime',
        'oferta_activa'      => 'boolean',
        'fuera_de_servicio'  => 'boolean',
        'destacado_home'     => 'boolean',
    ];

    /**
     * Cast manual (no el 'array' genérico): ese escapa acentos como ó al guardar,
     * lo que rompe la búsqueda por palabra completa (REGEXP \b) — "bar" quedaba "separado"
     * de "ón" en "Barón" porque la barra invertida cuenta como límite de palabra.
     */
    protected function tags(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? json_decode($value, true) : [],
            set: fn ($value) => json_encode($value ?? [], JSON_UNESCAPED_UNICODE),
        );
    }

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
        'estado_aprobacion',
        'contacto_whatsapp',
        'eliminado',
        'es_cliente',
        'modulos_habilitados',
        'oferta_del_dia',
        'oferta_activa',
        'oferta_expira_at',
        'descripcion_busqueda',
        'descripcion_busqueda_admin',
        'imagen_perfil',
        'fuera_de_servicio',
        'fuera_de_servicio_motivo',
        'destacado_home',
        'orden_destacado',
    ];

    /**
     * IDs de puntos de ejemplo/demo — no son negocios reales, se crearon para que un cliente
     * prospecto viera cómo quedaría su local antes de registrarse. Se muestran a propósito
     * en PublicitaController y ContactoController (las páginas de "mira un ejemplo"); en
     * cualquier otro lugar público (mapa, sitemap, listados, búsqueda, ficha directa) deben
     * quedar afuera — usar siempre scopePublico() en vez de armar los where() a mano.
     *
     * Editable desde /admin/configuracion (no hardcodeado) — se guarda como string
     * separado por comas en Configuracion::get('puntos_demo_excluidos').
     */
    public static function idsExcluidos(): array
    {
        $valor = Configuracion::get('puntos_demo_excluidos', '');

        return collect(explode(',', $valor))
            ->map(fn($id) => (int) trim($id))
            ->filter()
            ->values()
            ->all();
    }

    public function scopeSinExcluidos($query)
    {
        $ids = static::idsExcluidos();

        return $ids ? $query->whereNotIn('id', $ids) : $query;
    }

    /**
     * Único lugar que define "punto visible públicamente": activo, no eliminado, y sin los
     * IDs de ejemplo/demo. Reemplaza el patrón repetido where('activo',1)->where('eliminado',false)
     * [->sinExcluidos()] — así cambiar la definición de "visible" solo requiere tocar acá.
     */
    public function scopePublico($query)
    {
        return $query->where('activo', true)->where('eliminado', false)->sinExcluidos();
    }

    /**
     * Igual que scopePublico(), pero deja pasar los puntos de ejemplo/demo si la sesión
     * viene de /contacto o /registro (ContactoController y PublicitaController marcan la
     * sesión al cargar esas páginas). Solo la ficha directa (PuntoInteresController::show)
     * usa esto — mapa, sitemap, listados y búsqueda deben seguir usando scopePublico() sin
     * excepción.
     */
    public function scopeVisibleFicha($query)
    {
        if (session('demo_ficha_ok')) {
            return $query->where('activo', true)->where('eliminado', false);
        }

        return $query->publico();
    }

    /**
     * Búsqueda de texto libre, ordenada por relevancia (no por fecha).
     * Prioridad: título > descripción curada (cliente o admin) > sector/dirección.
     * No busca en "description" (la reseña larga) — daba demasiados falsos positivos
     * (ej. un mural que menciona "color café" apareciendo al buscar "café").
     * descripcion_busqueda la rellena el cliente desde su panel; descripcion_busqueda_admin
     * es la misma idea pero solo editable desde el admin (útil para puntos sin cliente asociado).
     *
     * Usa coincidencia de palabra completa (REGEXP con \b), no substring — un LIKE '%bar%'
     * hacía match con "Cerro Barón" o "Barrio Puerto" (sectores reales de Valparaíso) y
     * mostraba iglesias/plazas sin ninguna relación al buscar "bar". Se tolera un plural
     * simple (s/es) para que "mirador" siga encontrando "miradores".
     *
     * El REGEXP de MySQL 8 (motor ICU) no ignora tildes como sí lo hacía el LIKE anterior
     * (con la collation actual, "cafe" SÍ encontraba "café" vía LIKE, pero no vía REGEXP) —
     * por eso cada vocal se expande a una clase [aá] para tolerar ambas formas.
     */
    public function scopeBuscar($query, string $termino)
    {
        $lower  = mb_strtolower(trim($termino));
        $patron = '\\b' . static::patronTolerante($lower) . '(es|s)?\\b';

        return $query
            ->where(function ($q) use ($patron) {
                $q->whereRaw('LOWER(title) REGEXP ?', [$patron])
                  ->orWhereRaw('LOWER(descripcion_busqueda) REGEXP ?', [$patron])
                  ->orWhereRaw('LOWER(descripcion_busqueda_admin) REGEXP ?', [$patron])
                  ->orWhereRaw('LOWER(tags) REGEXP ?', [$patron])
                  ->orWhereRaw('LOWER(sector) REGEXP ?', [$patron])
                  ->orWhereRaw('LOWER(direccion) REGEXP ?', [$patron]);
            })
            ->selectRaw('puntosinteres.*, (CASE
                WHEN LOWER(title) REGEXP ? THEN 1
                WHEN LOWER(descripcion_busqueda) REGEXP ? THEN 2
                WHEN LOWER(descripcion_busqueda_admin) REGEXP ? THEN 2
                WHEN LOWER(sector) REGEXP ? THEN 3
                WHEN LOWER(direccion) REGEXP ? THEN 4
                ELSE 5
            END) as relevancia_busqueda', [$patron, $patron, $patron, $patron, $patron])
            ->orderBy('relevancia_busqueda');
    }

    /** Arma un patrón REGEXP tolerante a tildes: cada vocal (y ñ) matchea con o sin acento. */
    private static function patronTolerante(string $termino): string
    {
        $sinAcentos = strtr($termino, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
        $variantes  = ['a' => '[aá]', 'e' => '[eé]', 'i' => '[ií]', 'o' => '[oó]', 'u' => '[uúü]', 'n' => '[nñ]'];

        $partes = [];
        foreach (mb_str_split($sinAcentos) as $char) {
            $partes[] = $variantes[$char] ?? preg_quote($char, '/');
        }

        return implode('', $partes);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // CATÁLOGOS DE MÓDULOS
    // ═══════════════════════════════════════════════════════════════════════════

    /** Catálogo completo de módulos disponibles para clientes. */
    public static function catalogoModulos(): array
    {
        return [
            // Transversales — disponibles para todos los clientes
            'oferta_del_dia' => ['label' => 'Oferta del día',          'emoji' => '🏷️', 'desc' => 'Oferta puntual visible en la ficha',        'grupo' => 'Transversal'],
            'avisos'         => ['label' => 'Avisos',                  'emoji' => '📢', 'desc' => 'Comunicados y avisos importantes',           'grupo' => 'Transversal'],
            'promociones'    => ['label' => 'Promociones',             'emoji' => '🎁', 'desc' => 'Descuentos y promociones especiales',        'grupo' => 'Transversal'],
            'agenda'         => ['label' => 'Agenda cultural',         'emoji' => '📅', 'desc' => 'Programación de eventos y espectáculos',     'grupo' => 'Transversal'],
            // Gastronomía — restaurantes, cafeterías, bares
            'menu_del_dia'   => ['label' => 'Menú del día',            'emoji' => '🥘', 'desc' => 'Menú de almuerzo o cena del día',            'grupo' => 'Gastronomía'],
            'carta'          => ['label' => 'Carta / Menú permanente', 'emoji' => '🍽️', 'desc' => 'Carta completa del local',                   'grupo' => 'Gastronomía'],
            // Alojamiento
            'habitaciones'   => ['label' => 'Habitaciones y precios',  'emoji' => '🛏️', 'desc' => 'Tipos de habitación y tarifas',              'grupo' => 'Alojamiento'],
            'servicios'      => ['label' => 'Servicios incluidos',     'emoji' => '✨', 'desc' => 'Amenidades del alojamiento',                 'grupo' => 'Alojamiento'],
            'politicas'      => ['label' => 'Políticas',               'emoji' => '📋', 'desc' => 'Check-in/out, cancelación, normas',          'grupo' => 'Alojamiento'],
            // Museo
            'entradas'       => ['label' => 'Entradas y tarifas',      'emoji' => '🎟️', 'desc' => 'Precios de entrada al museo',                'grupo' => 'Museo'],
            'exposiciones'   => ['label' => 'Exposiciones',            'emoji' => '🖼️', 'desc' => 'Colecciones permanentes y temporales',       'grupo' => 'Museo'],
        ];
    }

    /** Módulos activos por defecto al activar un cliente, según su categoría (leídos de BD). */
    public static function modulosDefecto(int $categoriaId): array
    {
        $cat = \App\Models\Categoria::find($categoriaId);
        return $cat?->modulos_defecto ?? [];
    }

    /**
     * Módulos disponibles para selección según la categoría.
     * Define el catálogo filtrado que ve el admin al asignar módulos.
     */
    public static function modulosDisponibles(int $categoriaId): array
    {
        $gruposPermitidos = match (true) {
            in_array($categoriaId, [2, 8, 10]) => ['Transversal', 'Gastronomía'],
            $categoriaId === 11                 => ['Transversal', 'Alojamiento'],
            $categoriaId === 7                  => ['Transversal', 'Museo'],
            $categoriaId === 5                  => ['Transversal', 'Cultura'],
            default                             => ['Transversal'],
        };

        return array_filter(
            static::catalogoModulos(),
            fn($m) => in_array($m['grupo'], $gruposPermitidos)
        );
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

    public function operadores()
    {
        return $this->belongsToMany(OperadorTuristico::class, 'operador_punto_interes', 'punto_interes_id', 'operador_turistico_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenPunto::class, 'punto_interes_id');
    }

    public function productos()
    {
        return $this->hasMany(PuntoProducto::class, 'punto_interes_id')->orderBy('orden');
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

    public function recomendaciones()
    {
        return $this->hasMany(Recomendacion::class, 'punto_interes_id');
    }

    public function actividades()
    {
        return $this->hasMany(ActividadCliente::class, 'punto_interes_id');
    }

    /** Reseña publicada de Pindoor Recomienda asociada a este lugar, si existe. */
    public function recomendacionPublicada(): ?Recomendacion
    {
        return $this->recomendaciones()->publicadas()->orderByDesc('destacado_portada')->first();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // HELPERS DE MÓDULOS
    // ═══════════════════════════════════════════════════════════════════════════

    /** Comprueba si un módulo está habilitado para este punto. */
    public function moduloActivo(string $modulo): bool
    {
        return !$this->esBasico() && in_array($modulo, $this->modulos_habilitados ?? []);
    }

    /** True si el punto es un perfil básico (sin dueño real, aún no reclamado). */
    public function esBasico(): bool
    {
        return (bool) $this->usuario?->es_sistema;
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
            && (!empty($carta['texto']) || !empty($carta['pdf_ruta']) || !empty($carta['url']));
    }

    public function tieneMenu(): bool
    {
        return $this->es_cliente
            && $this->moduloActivo('menu_del_dia')
            && !empty($this->dato('menu_del_dia')['texto'] ?? '');
    }

    public function tieneAvisos(): bool
    {
        return $this->es_cliente
            && $this->moduloActivo('avisos')
            && !empty($this->dato('avisos')['texto'] ?? '');
    }

    public function tienePromociones(): bool
    {
        return $this->es_cliente
            && $this->moduloActivo('promociones')
            && !empty($this->dato('promociones')['texto'] ?? '');
    }

        public function tieneAgenda(): bool
    {
        return $this->es_cliente
            && $this->moduloActivo('agenda')
            && !empty($this->dato('agenda')['texto'] ?? '');
    }

            public function tieneExposiciones(): bool
    {
        return $this->es_cliente
            && $this->moduloActivo('exposiciones')
            && !empty($this->dato('exposiciones')['texto'] ?? '');
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

    public function scopeDestacadosHome($query)
    {
        return $query->clientes()->where('destacado_home', true)->orderBy('orden_destacado');
    }

    public function scopeAtractivos($query)
    {
        return $query->where('es_cliente', false);
    }
}
