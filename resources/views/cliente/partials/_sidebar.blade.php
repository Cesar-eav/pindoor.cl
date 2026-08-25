@php
    $punto->loadMissing('imagenes');

    $logoUrl = $punto->imagen_perfil
        ? asset('storage/'.$punto->imagen_perfil)
        : (auth()->user()->imagen_logo ? asset('storage/'.auth()->user()->imagen_logo) : null);

    $modulos   = $punto->modulos_habilitados ?? [];
    $datoCarta = $punto->dato('carta');

    $tieneModuloAlojamiento = count(array_intersect(['habitaciones','servicios','politicas'], $modulos)) > 0;
    $tieneActividadDiaria   = count(array_intersect(['oferta_del_dia','menu_del_dia','avisos','promociones'], $modulos)) > 0;
    $tieneContenido         = in_array('carta', $modulos) || in_array('entradas', $modulos) || in_array('exposiciones', $modulos) || in_array('agenda', $modulos) || in_array($punto->categoria_id, [13,14]);

    $imagenes = $punto->imagenes;

    $checks = [
        'imagen_perfil' => (bool) $logoUrl,
        'description'   => !empty(trim(strip_tags($punto->description ?? ''))),
        'galeria'       => $imagenes->isNotEmpty(),
        'horario'       => !empty(trim($punto->horario ?? '')),
        'direccion'     => !empty(trim($punto->direccion ?? '')),
        'sector'        => !empty(trim($punto->sector ?? '')),
    ];
    if ($datoCarta !== null) {
        $checks['carta'] = !empty($datoCarta['url'] ?? '') || !empty($datoCarta['texto'] ?? '');
    }
    $pendientesCount = collect($checks)->reject(fn($ok) => $ok)->count();
    $tieneRecomienda = $punto->recomendaciones()->exists();

    // Las anclas (#galeria, #museo, etc.) solo existen en cliente.perfil.ver —
    // se arma la URL absoluta para que estos enlaces también funcionen desde
    // otras páginas del panel (ej. /eventos) que comparten este sidebar.
    $perfilUrl = route('cliente.perfil.ver', $punto);

    // Cuando este partial se incluye desde cliente/perfil.blade.php (único
    // lugar con el x-data="perfilTabs(...)" que expone goTo()), los enlaces
    // ancla cambian de pestaña sin recargar. Desde cualquier otra página
    // (ej. cliente/eventos.blade.php) se dejan como navegación normal.
    $inPerfilTabs = $inPerfilTabs ?? false;
@endphp

<style>
.sidebar-group-label {
    font-size: 0.65rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 0 0.75rem;
    margin-bottom: 0.375rem;
    display: block;
}
.sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.625rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: rgba(255,255,255,0.6);
    transition: all 0.15s;
    text-decoration: none;
}
.sidebar-link:hover { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.95); }
.sidebar-link.active { background: rgba(252,86,72,0.18); color: #fc5648; font-weight: 700; }
.sidebar-dot { width: 0.5rem; height: 0.5rem; background: #f59e0b; border-radius: 9999px; flex-shrink: 0; margin-left: auto; }
</style>

<aside class="hidden lg:flex flex-col w-60 shrink-0 sticky top-14 overflow-y-auto" style="height: calc(100vh - 3.5rem); background: #0f172a;">

    {{-- Identidad del negocio --}}
    <div class="p-5" style="border-bottom: 1px solid rgba(255,255,255,0.08)">
        <div class="flex items-center gap-3">

            {{-- Logo clickeable — mini-form, XHR con preview y progreso --}}
            <form id="logo-sidebar-form" method="POST" action="{{ route('cliente.perfil.actualizar', $punto) }}" enctype="multipart/form-data" class="shrink-0">
                @csrf @method('PUT')
                <input type="file" id="logo-sidebar-input" name="imagen_perfil"
                       accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="hidden">
                <label for="logo-sidebar-input" class="relative cursor-pointer group block w-11 h-11">
                    @if($logoUrl)
                        <img id="logo-sidebar-preview" src="{{ $logoUrl }}" alt="Logo" class="w-11 h-11 rounded-xl object-cover" style="border: 2px solid rgba(255,255,255,0.15)">
                    @else
                        <div id="logo-sidebar-placeholder" class="w-11 h-11 rounded-xl flex items-center justify-center text-xl" style="background: rgba(255,255,255,0.08)">🏪</div>
                    @endif
                    {{-- Overlay cámara --}}
                    <div id="logo-camera-overlay" class="absolute inset-0 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity" style="background: rgba(0,0,0,0.55)">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    {{-- Overlay progreso --}}
                    <div id="logo-progress-overlay" class="absolute inset-0 rounded-xl flex flex-col items-center justify-center hidden" style="background: rgba(0,0,0,0.72)">
                        <span id="logo-progress-pct" class="text-white font-black" style="font-size:0.65rem; line-height:1">0%</span>
                    </div>
                </label>
            </form>

            <div class="min-w-0">
                <p class="text-sm font-bold text-white truncate leading-tight">{{ $punto->title }}</p>
                <span class="inline-block mt-1 text-[11px] px-2 py-0.5 rounded-full font-bold {{ $punto->activo ? 'bg-green-500/20 text-green-400' : 'bg-orange-500/20 text-orange-400' }}">
                    {{ $punto->activo ? '● Visible' : '● Pausado' }}
                </span>
            </div>
        </div>
        @if($pendientesCount > 0)
        <div class="mt-3 rounded-xl px-3 py-2" style="background: rgba(252,86,72,0.15); border: 1px solid rgba(252,86,72,0.3)">
            <p class="text-[11px] font-bold" style="color: #fc5648">⚠ {{ $pendientesCount }} campo{{ $pendientesCount > 1 ? 's' : '' }} pendiente{{ $pendientesCount > 1 ? 's' : '' }}</p>
        </div>
        @endif
    </div>

    {{-- Navegación --}}
    <nav class="flex-1 p-3 space-y-6 overflow-y-auto">

        @if($tieneActividadDiaria)
        <div>
            <p class="sidebar-group-label" style="color: #fc5648">Actividad de hoy</p>
            <div class="space-y-0.5">
                @if(in_array('oferta_del_dia', $modulos))
                <a href="{{ $perfilUrl }}#oferta" class="sidebar-link" @if($inPerfilTabs) @click.prevent="goTo('actividad', 'oferta')" @endif>🏷️ Oferta del día</a>
                @endif
                @if(in_array('menu_del_dia', $modulos))
                <a href="{{ $perfilUrl }}#menu" class="sidebar-link" @if($inPerfilTabs) @click.prevent="goTo('actividad', 'menu')" @endif>🥘 Menú del día</a>
                @endif
                @if(in_array('avisos', $modulos))
                <a href="{{ $perfilUrl }}#avisos" class="sidebar-link" @if($inPerfilTabs) @click.prevent="goTo('actividad', 'avisos')" @endif>📢 Avisos</a>
                @endif
                @if(in_array('promociones', $modulos))
                <a href="{{ $perfilUrl }}#promociones" class="sidebar-link" @if($inPerfilTabs) @click.prevent="goTo('actividad', 'promociones')" @endif>🎁 Promociones</a>
                @endif
            </div>
        </div>
        @endif

        @if($tieneRecomienda)
        <div>
            <p class="sidebar-group-label" style="color: #a855f7">Pindoor Recomienda</p>
            <div class="space-y-0.5">
                <a href="{{ $perfilUrl }}#recomienda" class="sidebar-link" @if($inPerfilTabs) @click.prevent="goTo('recomienda', 'recomienda')" @endif>📰 Mi(s) publicación(es)</a>
            </div>
        </div>
        @endif

        <div>
            <p class="sidebar-group-label" style="color: #60a5fa">Tu perfil</p>
            <div class="space-y-0.5">
                <a href="{{ $perfilUrl }}#galeria" class="sidebar-link justify-between" @if($inPerfilTabs) @click.prevent="goTo('perfil', 'galeria')" @endif>
                    <span>🖼️ Galería</span>
                    @if(!$checks['galeria'])<span class="sidebar-dot"></span>@endif
                </a>
                <a href="{{ $perfilUrl }}#imagen-perfil" class="sidebar-link justify-between" @if($inPerfilTabs) @click.prevent="goTo('perfil', 'imagen-perfil')" @endif>
                    <span>🏷️ Logo / imagen</span>
                    @if(!$checks['imagen_perfil'])<span class="sidebar-dot"></span>@endif
                </a>
                <a href="{{ $perfilUrl }}#descripcion" class="sidebar-link justify-between" @if($inPerfilTabs) @click.prevent="goTo('perfil', 'descripcion')" @endif>
                    <span>📝 Descripción</span>
                    @if(!$checks['description'])<span class="sidebar-dot"></span>@endif
                </a>
                <a href="{{ $perfilUrl }}#ubicacion" class="sidebar-link justify-between" @if($inPerfilTabs) @click.prevent="goTo('perfil', 'ubicacion')" @endif>
                    <span>📍 Ubicación</span>
                    @if(!$checks['horario'] || !$checks['direccion'] || !$checks['sector'])<span class="sidebar-dot"></span>@endif
                </a>
                <a href="{{ $perfilUrl }}#contacto" class="sidebar-link" @if($inPerfilTabs) @click.prevent="goTo('perfil', 'contacto')" @endif>🔗 Contacto y redes</a>
                <a href="{{ $perfilUrl }}#busqueda" class="sidebar-link" @if($inPerfilTabs) @click.prevent="goTo('perfil', 'busqueda')" @endif>🔍 Búsqueda SEO</a>
            </div>
        </div>

        @if($tieneContenido || $tieneModuloAlojamiento)
        <div>
            <p class="sidebar-group-label" style="color: #34d399">Contenido</p>
            <div class="space-y-0.5">
                @if(in_array('carta', $modulos))
                <a href="{{ $perfilUrl }}#carta" class="sidebar-link justify-between" @if($inPerfilTabs) @click.prevent="goTo('contenido', 'carta')" @endif>
                    <span>🍽️ Carta / Menú</span>
                    @if(isset($checks['carta']) && !$checks['carta'])<span class="sidebar-dot"></span>@endif
                </a>
                @endif
                @if($tieneModuloAlojamiento)
                <a href="{{ $perfilUrl }}#alojamiento" class="sidebar-link" @if($inPerfilTabs) @click.prevent="goTo('contenido', 'alojamiento')" @endif>🛏️ Alojamiento</a>
                @endif
                @if(in_array('entradas', $modulos) || in_array('exposiciones', $modulos))
                <a href="{{ $perfilUrl }}#museo" class="sidebar-link" @if($inPerfilTabs) @click.prevent="goTo('contenido', 'museo')" @endif>🎟️ Museo</a>
                @endif
                @if(in_array('agenda', $modulos))
                <a href="{{ route('cliente.eventos.index', $punto) }}" class="sidebar-link {{ request()->routeIs('cliente.eventos.index') ? 'active' : '' }}">📅 Eventos</a>
                @endif
                @if(in_array($punto->categoria_id, [13, 14]))
                <a href="{{ $perfilUrl }}#catalogo" class="sidebar-link" @if($inPerfilTabs) @click.prevent="goTo('contenido', 'catalogo')" @endif>🛍️ Catálogo</a>
                @endif
            </div>
        </div>
        @endif

    </nav>

    {{-- Footer --}}
    <div class="p-4 space-y-2" style="border-top: 1px solid rgba(255,255,255,0.08)">
        <a href="{{ route('puntos.show', $punto->slug) }}" target="_blank"
           class="flex items-center justify-between text-xs font-medium transition"
           style="color: rgba(255,255,255,0.5)"
           onmouseover="this.style.color='#fc5648'" onmouseout="this.style.color='rgba(255,255,255,0.5)'">
            Ver ficha pública
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
        @if(auth()->user()->puntoInteres()->count() > 1)
        <a href="{{ route('cliente.perfil') }}" class="block text-xs transition" style="color: rgba(255,255,255,0.35)">← Mis negocios</a>
        @endif
    </div>

</aside>
