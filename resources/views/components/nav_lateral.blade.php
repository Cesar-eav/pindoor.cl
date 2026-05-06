{{-- Sidebar lateral — solo visible en md+ --}}
<aside class="hidden md:flex flex-col w-56 fixed top-0 left-0 h-full bg-white border-r border-gray-100 z-40 font-sans">

    {{-- Brand --}}
    <div class="px-5 py-5 border-b border-gray-100">
        <a href="{{ route('puntos.index') }}" class="flex flex-col gap-0.5">
            <span class="text-2xl font-black tracking-tight leading-none">
                <span class="text-[#fc5648]">Pin</span><span class="text-gray-900">door</span>
            </span>
            <span class="text-[10px] text-gray-400 font-semibold tracking-wider uppercase mt-1">
                Road House Blues
            </span>
        </a>
    </div>

    {{-- Navegación --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

        @php
            $navItems = [
                [
                    'route'  => 'puntos.index',
                    'label'  => 'Inicio',
                    'icon'   => '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a2 2 0 002 2h10a2 2 0 002-2V10"/></svg>',
                    'match'  => ['puntos.index', 'puntos.index'],
                ],
                [
                    'route'  => 'puntos.index',
                    'label'  => 'Explorar mapa',
                    'icon'   => '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>',
                    'match'  => ['atractivos.show', 'puntos.show'],
                    'onclick' => true,
                ],
                [
                    'route'  => 'atractivos.panoramas',
                    'label'  => 'Panoramas',
                    'icon'   => '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                    'match'  => ['atractivos.panoramas'],
                ],
            ];
        @endphp

        @foreach($navItems as $item)
            @php $isActive = request()->routeIs($item['match']); @endphp
            @if(!empty($item['onclick']))
            <a href="{{ route($item['route']) }}"
               onclick="if(typeof setView!=='undefined'){event.preventDefault();setView('mapa');}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all
                      {{ $isActive ? 'bg-[#fff0ef] text-[#fc5648]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                {!! $item['icon'] !!}
                {{ $item['label'] }}
            </a>
            @else
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all
                      {{ $isActive ? 'bg-[#fff0ef] text-[#fc5648]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                {!! $item['icon'] !!}
                {{ $item['label'] }}
            </a>
            @endif
        @endforeach

        <div class="pt-3 mt-3 border-t border-gray-100">
            <a href="{{ route('publicita.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all
                      {{ request()->routeIs('publicita.index') ? 'bg-[#fff0ef] text-[#fc5648]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                Registra tu negocio
            </a>
        </div>

    </nav>

    {{-- Footer --}}
    <div class="px-5 py-4 border-t border-gray-100">
        <p class="text-[10px] text-gray-400 text-center tracking-wide">
            © {{ date('Y') }} Pindoor.cl
        </p>
    </div>

</aside>
