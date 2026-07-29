<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <span class="text-xl font-black tracking-tight leading-none">
                        <span style="color:#fc5648">Pin</span><span class="text-gray-900">door</span>
                    </span>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    @if(auth()->user()->type === 'admin')
                        <x-nav-link :href="route('admin.stats')" :active="request()->routeIs('admin.stats')">
                            Stats
                        </x-nav-link>
                        <x-nav-link :href="route('admin.clientes')" :active="request()->routeIs('admin.clientes')">
                            Clientes
                        </x-nav-link>
                        <x-nav-link :href="route('admin.leads')" :active="request()->routeIs('admin.leads')">
                            Leads
                        </x-nav-link>
                        <x-nav-link :href="route('admin.usuarios')" :active="request()->routeIs('admin.usuarios')">
                            Usuarios
                        </x-nav-link>
                        <x-nav-link :href="route('admin.puntos.create')" :active="request()->routeIs('admin.puntos.create')">
                            Nuevo Punto
                        </x-nav-link>
                        <x-nav-link :href="route('admin.categorias.index')" :active="request()->routeIs('admin.categorias.*')">
                            Categorías
                        </x-nav-link>
                        <x-nav-link :href="route('admin.categoria-eventos.index')" :active="request()->routeIs('admin.categoria-eventos.*')">
                            Categorías de eventos
                        </x-nav-link>
                        <x-nav-link :href="route('admin.panoramas.index')" :active="request()->routeIs('admin.panoramas.*')">
                            Panoramas
                        </x-nav-link>
                        <x-nav-link :href="route('admin.experiencias.index')" :active="request()->routeIs('admin.experiencias.*')">
                            Experiencias
                        </x-nav-link>
                        <x-nav-link :href="route('admin.artistas')" :active="request()->routeIs('admin.artistas*')">
                            Artistas
                        </x-nav-link>
                        <x-nav-link :href="route('admin.blog.index')" :active="request()->routeIs('admin.blog*')">
                            Blog
                        </x-nav-link>
                        <x-nav-link :href="route('admin.recomendaciones.index')" :active="request()->routeIs('admin.recomendaciones*')">
                            Recomienda
                        </x-nav-link>



                    @endif

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button onclick="toggleMobileMenu()" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path id="hamburger-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path id="hamburger-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div data-mobile-menu class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">

            @if(auth()->user()->type === 'admin')
                <x-responsive-nav-link :href="route('admin.stats')" :active="request()->routeIs('admin.stats')">
                    Stats
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.clientes')" :active="request()->routeIs('admin.clientes')">
                    Clientes
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.leads')" :active="request()->routeIs('admin.leads')">
                    Leads
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.usuarios')" :active="request()->routeIs('admin.usuarios')">
                    Usuarios
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.puntos.create')" :active="request()->routeIs('admin.puntos.create')">
                    Nuevo Punto
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.categorias.index')" :active="request()->routeIs('admin.categorias.*')">
                    Categorías
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.categoria-eventos.index')" :active="request()->routeIs('admin.categoria-eventos.*')">
                    Categorías de eventos
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.panoramas.index')" :active="request()->routeIs('admin.panoramas.*')">
                    Panoramas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.experiencias.index')" :active="request()->routeIs('admin.experiencias.*')">
                    Experiencias
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.artistas')" :active="request()->routeIs('admin.artistas*')">
                    Artistas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.blog.index')" :active="request()->routeIs('admin.blog*')">
                    Blog
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.recomendaciones.index')" :active="request()->routeIs('admin.recomendaciones*')">
                    Recomienda
                </x-responsive-nav-link>

            @elseif(auth()->user()->type === 'cliente')
                @if(isset($punto, $modulos))
                    {{-- Menú contextual de la página de perfil --}}
                    @php
                        $_tieneActividadMob = count(array_intersect(['oferta_del_dia','menu_del_dia','avisos','promociones'], $modulos)) > 0;
                        $_tieneContenidoMob = in_array('carta', $modulos) || in_array('entradas', $modulos) || in_array('exposiciones', $modulos) || in_array('agenda', $modulos) || in_array($punto->categoria_id, [13,14]);
                        $_tieneAlojMob      = count(array_intersect(['habitaciones','servicios','politicas'], $modulos)) > 0;
                    @endphp

                    <div class="px-4 pt-3 pb-1 flex items-center justify-between">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $punto->title }}</p>
                        <a href="{{ route('puntos.show', $punto->slug) }}" target="_blank"
                           class="text-[10px] text-[#fc5648] font-bold">Ver ficha ↗</a>
                    </div>

                    @if($_tieneActividadMob)
                    <div class="px-4 pt-2 pb-0.5">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Actividad de hoy</p>
                    </div>
                    @if(in_array('oferta_del_dia', $modulos))
                    <x-responsive-nav-link href="#oferta" onclick="toggleMobileMenu()">🏷️ Oferta del día</x-responsive-nav-link>
                    @endif
                    @if(in_array('menu_del_dia', $modulos))
                    <x-responsive-nav-link href="#menu" onclick="toggleMobileMenu()">🥘 Menú del día</x-responsive-nav-link>
                    @endif
                    @if(in_array('avisos', $modulos))
                    <x-responsive-nav-link href="#avisos" onclick="toggleMobileMenu()">📢 Avisos</x-responsive-nav-link>
                    @endif
                    @if(in_array('promociones', $modulos))
                    <x-responsive-nav-link href="#promociones" onclick="toggleMobileMenu()">🎁 Promociones</x-responsive-nav-link>
                    @endif
                    @endif

                    <div class="px-4 pt-3 pb-0.5">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tu perfil</p>
                    </div>
                    <x-responsive-nav-link href="#galeria"       onclick="toggleMobileMenu()">🖼️ Galería</x-responsive-nav-link>
                    <x-responsive-nav-link href="#imagen-perfil" onclick="toggleMobileMenu()">🏷️ Logo / imagen</x-responsive-nav-link>
                    <x-responsive-nav-link href="#descripcion"   onclick="toggleMobileMenu()">📝 Descripción</x-responsive-nav-link>
                    <x-responsive-nav-link href="#ubicacion"     onclick="toggleMobileMenu()">📍 Ubicación</x-responsive-nav-link>
                    <x-responsive-nav-link href="#contacto"      onclick="toggleMobileMenu()">🔗 Contacto</x-responsive-nav-link>
                    <x-responsive-nav-link href="#busqueda"      onclick="toggleMobileMenu()">🔍 Búsqueda SEO</x-responsive-nav-link>

                    @if($_tieneContenidoMob || $_tieneAlojMob)
                    <div class="px-4 pt-3 pb-0.5">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Contenido</p>
                    </div>
                    @if(in_array('carta', $modulos))
                    <x-responsive-nav-link href="#carta" onclick="toggleMobileMenu()">🍽️ Carta / Menú</x-responsive-nav-link>
                    @endif
                    @if($_tieneAlojMob)
                    <x-responsive-nav-link href="#alojamiento" onclick="toggleMobileMenu()">🛏️ Alojamiento</x-responsive-nav-link>
                    @endif
                    @if(in_array('entradas', $modulos) || in_array('exposiciones', $modulos))
                    <x-responsive-nav-link :href="route('cliente.museo', $punto)">🎟️ Museo ↗</x-responsive-nav-link>
                    @endif
                    @if(in_array('agenda', $modulos))
                    <x-responsive-nav-link :href="route('cliente.eventos', $punto)">📅 Eventos ↗</x-responsive-nav-link>
                    @endif
                    @if(in_array($punto->categoria_id, [13,14]))
                    <x-responsive-nav-link :href="route('cliente.productos.index')">🛍️ Catálogo ↗</x-responsive-nav-link>
                    @endif
                    @endif

                @else
                    <x-responsive-nav-link :href="route('cliente.perfil')" :active="request()->routeIs('cliente.*')">
                        Mi Negocio
                    </x-responsive-nav-link>
                @endif

            @endif

        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
<script>
function toggleMobileMenu() {
    const menu  = document.querySelector('[data-mobile-menu]');
    const open  = document.getElementById('hamburger-open');
    const close = document.getElementById('hamburger-close');
    menu.classList.toggle('hidden');
    open.classList.toggle('hidden');
    close.classList.toggle('hidden');
}
</script>
</nav>
