<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
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
                        <x-nav-link :href="route('admin.panoramas.index')" :active="request()->routeIs('admin.panoramas.*')">
                            Panoramas
                        </x-nav-link>

                    @elseif(auth()->user()->type === 'cliente')
                        <x-nav-link :href="route('cliente.perfil')" :active="request()->routeIs('cliente.*')">
                            Mi Negocio
                        </x-nav-link>

                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
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
                <button data-mobile-menu-button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path data-hamburger-open class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path data-hamburger-close class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                <x-responsive-nav-link :href="route('admin.panoramas.index')" :active="request()->routeIs('admin.panoramas.*')">
                    Panoramas
                </x-responsive-nav-link>

            @elseif(auth()->user()->type === 'cliente')
                <x-responsive-nav-link :href="route('cliente.perfil')" :active="request()->routeIs('cliente.*')">
                    Mi Negocio
                </x-responsive-nav-link>

            @else
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
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
</nav>
