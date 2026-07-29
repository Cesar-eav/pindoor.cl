<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')

        <style>
            .sidebar-group-label {
                font-size: 0.65rem;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                padding: 0 0.75rem;
                margin-bottom: 0.375rem;
                color: rgba(255,255,255,0.3);
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
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 lg:flex">

            {{-- ══════════════ Sidebar desktop ══════════════ --}}
            <aside class="hidden lg:flex flex-col w-60 shrink-0 sticky top-0 overflow-y-auto"
                   style="height: 100vh; background: #0f172a;">
                <div class="px-5 py-5" style="border-bottom: 1px solid rgba(255,255,255,0.08)">
                    <a href="{{ route('admin.stats') }}" class="text-lg font-black tracking-tight leading-none">
                        <span style="color:#fc5648">Pin</span><span class="text-white">door</span>
                        <span class="block text-[10px] font-bold text-white/30 tracking-widest mt-0.5">ADMIN</span>
                    </a>
                </div>

                <nav class="flex-1 px-3 py-4 space-y-5 overflow-y-auto">
                    <div class="space-y-0.5">
                        <a href="{{ route('admin.stats') }}" class="sidebar-link {{ request()->routeIs('admin.stats') ? 'active' : '' }}">📊 Stats</a>
                    </div>

                    <div>
                        <p class="sidebar-group-label">Negocios</p>
                        <div class="space-y-0.5">
                            <a href="{{ route('admin.clientes') }}" class="sidebar-link {{ request()->routeIs('admin.clientes') ? 'active' : '' }}">🏪 Clientes</a>
                            {{-- <a href="{{ route('admin.leads') }}" class="sidebar-link {{ request()->routeIs('admin.leads') ? 'active' : '' }}">📋 Leads</a> --}}
                            <a href="{{ route('admin.usuarios') }}" class="sidebar-link {{ request()->routeIs('admin.usuarios') ? 'active' : '' }}">👤 Usuarios</a>
                            <a href="{{ route('admin.puntos.create') }}" class="sidebar-link {{ request()->routeIs('admin.puntos.create') ? 'active' : '' }}">➕ Nuevo Punto</a>
                        </div>
                    </div>

                    <div>
                        <p class="sidebar-group-label">Catálogos</p>
                        <div class="space-y-0.5">
                            <a href="{{ route('admin.categorias.index') }}" class="sidebar-link {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}">🏷️ Categorías</a>
                            <a href="{{ route('admin.categoria-eventos.index') }}" class="sidebar-link {{ request()->routeIs('admin.categoria-eventos.*') ? 'active' : '' }}">🎉 Categorías de eventos</a>
                            <a href="{{ route('admin.configuracion.index') }}" class="sidebar-link {{ request()->routeIs('admin.configuracion.*') ? 'active' : '' }}">⚙️ Configuración</a>
                        </div>
                    </div>

                    <div>
                        <p class="sidebar-group-label">Contenido</p>
                        <div class="space-y-0.5">
                            <a href="{{ route('admin.panoramas.index') }}" class="sidebar-link {{ request()->routeIs('admin.panoramas.*') ? 'active' : '' }}">🌅 Panoramas</a>
                            <a href="{{ route('admin.experiencias.index') }}" class="sidebar-link {{ request()->routeIs('admin.experiencias.*') ? 'active' : '' }}">✨ Experiencias</a>
                            <a href="{{ route('admin.artistas') }}" class="sidebar-link {{ request()->routeIs('admin.artistas*') ? 'active' : '' }}">🎨 Artistas</a>
                            <a href="{{ route('admin.blog.index') }}" class="sidebar-link {{ request()->routeIs('admin.blog*') ? 'active' : '' }}">📰 Blog</a>
                            <a href="{{ route('admin.recomendaciones.index') }}" class="sidebar-link {{ request()->routeIs('admin.recomendaciones*') ? 'active' : '' }}">⭐ Recomienda</a>
                        </div>
                    </div>
                </nav>

                <div class="px-3 py-4" style="border-top: 1px solid rgba(255,255,255,0.08)">
                    <p class="px-3 text-xs font-bold text-white/70 truncate mb-1">{{ Auth::user()->name }}</p>
                    <a href="{{ route('profile.edit') }}" class="sidebar-link">🙍 Mi perfil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="sidebar-link w-full text-left">🚪 Cerrar sesión</button>
                    </form>
                </div>
            </aside>

            {{-- ══════════════ Barra mobile ══════════════ --}}
            <div class="lg:hidden bg-white border-b border-gray-100" x-data="{ open: false }">
                <div class="flex items-center justify-between px-4 h-16">
                    <span class="text-lg font-black tracking-tight leading-none">
                        <span style="color:#fc5648">Pin</span><span class="text-gray-900">door</span>
                    </span>
                    <button @click="open = !open" class="p-2 text-gray-500">
                        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div x-show="open" x-cloak style="background:#0f172a" class="px-3 py-4 space-y-5">
                    <div class="space-y-0.5">
                        <a href="{{ route('admin.stats') }}" class="sidebar-link {{ request()->routeIs('admin.stats') ? 'active' : '' }}">📊 Stats</a>
                    </div>
                    <div>
                        <p class="sidebar-group-label">Negocios</p>
                        <div class="space-y-0.5">
                            <a href="{{ route('admin.clientes') }}" class="sidebar-link {{ request()->routeIs('admin.clientes') ? 'active' : '' }}">🏪 Clientes</a>
                            <a href="{{ route('admin.leads') }}" class="sidebar-link {{ request()->routeIs('admin.leads') ? 'active' : '' }}">📋 Leads</a>
                            <a href="{{ route('admin.usuarios') }}" class="sidebar-link {{ request()->routeIs('admin.usuarios') ? 'active' : '' }}">👤 Usuarios</a>
                            <a href="{{ route('admin.puntos.create') }}" class="sidebar-link {{ request()->routeIs('admin.puntos.create') ? 'active' : '' }}">➕ Nuevo Punto</a>
                        </div>
                    </div>
                    <div>
                        <p class="sidebar-group-label">Catálogos</p>
                        <div class="space-y-0.5">
                            <a href="{{ route('admin.categorias.index') }}" class="sidebar-link {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}">🏷️ Categorías</a>
                            <a href="{{ route('admin.categoria-eventos.index') }}" class="sidebar-link {{ request()->routeIs('admin.categoria-eventos.*') ? 'active' : '' }}">🎉 Categorías de eventos</a>
                            <a href="{{ route('admin.configuracion.index') }}" class="sidebar-link {{ request()->routeIs('admin.configuracion.*') ? 'active' : '' }}">⚙️ Configuración</a>
                        </div>
                    </div>
                    <div>
                        <p class="sidebar-group-label">Contenido</p>
                        <div class="space-y-0.5">
                            <a href="{{ route('admin.panoramas.index') }}" class="sidebar-link {{ request()->routeIs('admin.panoramas.*') ? 'active' : '' }}">🌅 Panoramas</a>
                            <a href="{{ route('admin.experiencias.index') }}" class="sidebar-link {{ request()->routeIs('admin.experiencias.*') ? 'active' : '' }}">✨ Experiencias</a>
                            <a href="{{ route('admin.artistas') }}" class="sidebar-link {{ request()->routeIs('admin.artistas*') ? 'active' : '' }}">🎨 Artistas</a>
                            <a href="{{ route('admin.blog.index') }}" class="sidebar-link {{ request()->routeIs('admin.blog*') ? 'active' : '' }}">📰 Blog</a>
                            <a href="{{ route('admin.recomendaciones.index') }}" class="sidebar-link {{ request()->routeIs('admin.recomendaciones*') ? 'active' : '' }}">⭐ Recomienda</a>
                        </div>
                    </div>
                    <div style="border-top: 1px solid rgba(255,255,255,0.08)" class="pt-3">
                        <a href="{{ route('profile.edit') }}" class="sidebar-link">🙍 Mi perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="sidebar-link w-full text-left">🚪 Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ══════════════ Contenido ══════════════ --}}
            <main class="flex-1 min-w-0">
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
