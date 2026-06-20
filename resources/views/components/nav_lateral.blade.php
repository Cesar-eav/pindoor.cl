{{-- Sidebar lateral — solo visible en md+ --}}
<aside class="hidden md:flex flex-col w-56 fixed top-0 left-0 h-full bg-white border-r border-gray-100 z-40">

    {{-- Brand --}}
    <div class="px-5 py-5 border-b border-gray-100">
        <a href="{{ route('puntos.index') }}" class="flex flex-col gap-0.5">
            <span class="text-2xl font-black tracking-tight leading-none">
                <span class="text-[#fc5648]">Pin</span><span class="text-gray-900">door</span>
            </span>
            <span class="text-[10px] text-gray-400 font-semibold tracking-wider uppercase mt-1">
                V 0.0.1
            </span>
        </a>
    </div>

    {{-- Navegación --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

        @php
            $enMapa = request()->routeIs('puntos.index') && request('vista') === 'mapa';
            $navItems = [
                [
                    'href'   => route('puntos.index'),
                    'label'  => __('ui.nav.inicio'),
                    'icon'   => '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a2 2 0 002 2h10a2 2 0 002-2V10"/></svg>',
                    'active' => request()->routeIs('puntos.index') && !$enMapa,
                ],
                [
                    'href'    => route('puntos.index', ['vista' => 'mapa']),
                    'label'   => __('ui.nav.mapa'),
                    'icon'    => '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>',
                    'active'  => $enMapa,
                    'onclick' => "if(typeof setView!=='undefined'){event.preventDefault();setView('mapa');}",
                ],
                [
                    'href'   => route('atractivos.panoramas'),
                    'label'  => __('ui.nav.panoramas'),
                    'icon'   => '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                    'active' => request()->routeIs('atractivos.panoramas'),
                ],
                [
                    'href'   => route('artista.index'),
                    'label'  => 'La Escena',
                    'icon'   => '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>',
                    'active' => request()->routeIs('artista.index') || request()->routeIs('artista.show'),
                ],
                [
                    'href'   => route('experiencias.index'),
                    'label'  => __('ui.nav.experiencias'),
                    'icon'   => '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                    'active' => request()->routeIs('experiencias.*'),
                ],
                [
                    'href'   => route('blog.index'),
                    'label'  => __('ui.nav.blog'),
                    'icon'   => '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>',
                    'active' => request()->routeIs('blog.*'),
                ],
                [
                    'href'   => route('puntos.info'),
                    'label'  => __('ui.nav.info'),
                    'icon'   => '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                    'active' => request()->routeIs('puntos.info'),
                ],
                [
                    'href'   => route('contacto.index'),
                    'label'  => __('ui.nav.contacto'),
                    'icon'   => '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
                    'active' => request()->routeIs('contacto.index'),
                ],
            ];
        @endphp

        @php $navIds = ['nav-inicio', 'nav-explorar-mapa', 'nav-panoramas', 'nav-artistas', 'nav-experiencias', 'nav-blog', 'nav-info', 'nav-contacto']; @endphp
        @foreach($navItems as $i => $item)
            <a id="{{ $navIds[$i] }}"
               href="{{ $item['href'] }}"
               @if(!empty($item['onclick'])) onclick="{{ $item['onclick'] }}" @endif
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all
                      {{ $item['active'] ? 'bg-[#fff0ef] text-[#fc5648]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                {!! $item['icon'] !!}
                {{ $item['label'] }}
            </a>
        @endforeach

        {{-- <div class="pt-3 mt-3 border-t border-gray-100">
            <a href="{{ route('publicita.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all
                      {{ request()->routeIs('publicita.index') ? 'bg-[#fff0ef] text-[#fc5648]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                Registrate
            </a>
        </div> --}}

    </nav>

    {{-- Footer --}}
    <div class="px-5 py-4 border-t border-gray-100 space-y-2">
        <a href="https://www.instagram.com/pindoor.cl/" target="_blank" rel="noopener noreferrer"
           class="flex items-center gap-2 px-3 py-2 rounded-xl text-white text-xs font-bold transition hover:opacity-90 hover:scale-[1.02]"
           style="background: linear-gradient(135deg, #405DE6 0%, #833AB4 30%, #E1306C 60%, #F77737 85%, #FCAF45 100%);">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
            @pindoor.cl
        </a>
        <p class="text-[10px] text-gray-400 text-center tracking-wide">
            © {{ date('Y') }} Pindoor.cl
        </p>
    </div>

</aside>
