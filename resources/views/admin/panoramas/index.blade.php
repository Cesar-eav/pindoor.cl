<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panoramas</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.passline') }}"
                   class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-50 transition">
                    Importar Passline
                </a>
                <a href="{{ route('admin.panoramas.create') }}"
                   class="bg-[#fc5648] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#d94439] transition">
                    + Nuevo panorama
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Configuración periodo --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 flex flex-wrap items-center gap-4">
                <span class="text-sm font-semibold text-gray-600">🗓️ Periodo público:</span>
                <form action="{{ route('admin.panoramas.configuracion') }}" method="POST"
                      class="flex items-center gap-2">
                    @csrf
                    <span class="text-sm text-gray-500">Mostrar los próximos</span>
                    <input type="number" name="panoramas_limite_dias"
                           value="{{ $limiteDias }}" min="1" max="365"
                           class="w-16 px-2 py-1 border border-gray-200 rounded-lg text-sm text-center font-bold focus:ring-2 focus:ring-[#fc5648] outline-none">
                    <span class="text-sm text-gray-500">días</span>
                    <button type="submit"
                            class="bg-gray-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-gray-700 transition">
                        Guardar
                    </button>
                </form>
            </div>

            {{-- Búsqueda y filtros --}}
            <form method="GET" action="{{ route('admin.panoramas.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 space-y-3">
                <div class="flex gap-3">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Buscar por título o ubicación…"
                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                    </div>
                    @if($search || $categoria)
                    <a href="{{ route('admin.panoramas.index') }}"
                       class="flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 font-semibold px-3 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Limpiar
                    </a>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" name="categoria" value=""
                            class="px-3 py-1 rounded-full text-xs font-bold border transition
                                   {{ !$categoria ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400' }}">
                        Todos
                    </button>
                    @foreach($categorias as $slug => $cat)
                    <button type="submit" name="categoria" value="{{ $slug }}"
                            class="px-3 py-1 rounded-full text-xs font-bold border transition
                                   {{ $categoria === $slug ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400' }}">
                        {{ $cat['emoji'] }} {{ $cat['label'] }}
                    </button>
                    @endforeach
                    <button type="submit" name="categoria" value="gratuito"
                            class="px-3 py-1 rounded-full text-xs font-bold border transition
                                   {{ $categoria === 'gratuito' ? 'bg-green-700 text-white border-green-700' : 'bg-green-50 text-green-700 border-green-200 hover:border-green-400' }}">
                        🎟️ Gratis
                    </button>
                </div>
            </form>

            @if($panoramas->isEmpty())
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 px-5 py-16 text-center">
                    <div class="text-4xl mb-3">🔍</div>
                    @if($search || $categoria)
                        <p class="font-bold text-gray-700 mb-1">Sin resultados</p>
                        <a href="{{ route('admin.panoramas.index') }}"
                           class="text-[#fc5648] font-bold hover:underline text-sm">Ver todos</a>
                    @else
                        <p class="font-bold text-gray-700 mb-1">No hay panoramas</p>
                        <a href="{{ route('admin.panoramas.create') }}"
                           class="text-[#fc5648] font-bold hover:underline text-sm">Crea el primero</a>
                    @endif
                </div>
            @else

            @php
            $secciones = [
                'hoy'      => ['label' => 'Hoy',         'color' => 'bg-[#fc5648]',  'text' => 'text-white',     'open' => true],

                'semana'   => ['label' => 'Esta semana', 'color' => 'bg-amber-400',  'text' => 'text-amber-900', 'open' => true],
                'proximos' => ['label' => 'Próximos',    'color' => 'bg-blue-500',   'text' => 'text-white',     'open' => true],
                'pasados'  => ['label' => 'Pasados',     'color' => 'bg-gray-300',   'text' => 'text-gray-600',  'open' => false],
                'sin_fecha'=> ['label' => 'Sin fecha',   'color' => 'bg-gray-200',   'text' => 'text-gray-500',  'open' => true],
            ];
            @endphp

            @foreach($secciones as $key => $sec)
            @php $grupo = $grupos[$key]; @endphp
            @if($grupo->isNotEmpty())
            <details class="group/sec" {{ $sec['open'] ? 'open' : '' }}>
                <summary class="flex items-center gap-3 cursor-pointer select-none list-none mb-3">
                    <span class="inline-flex items-center gap-2 {{ $sec['color'] }} {{ $sec['text'] }} text-xs font-black uppercase tracking-widest px-3 py-1.5 rounded-full">
                        {{ $sec['label'] }}
                        <span class="opacity-70">{{ $grupo->count() }}</span>
                    </span>
                    <span class="flex-1 h-px bg-gray-200"></span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform group-open/sec:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>

                <div class="space-y-2">
                    @foreach($grupo as $panorama)
                    @php
                        $cat    = $panorama->categoria ? (\App\Models\Panorama::CATEGORIAS[$panorama->categoria] ?? null) : null;
                        $esHoy  = $panorama->fecha && $panorama->fecha->isSameDay($hoy);
                        $vencido = $key === 'pasados';
                    @endphp

                    <div x-data="{ eliminando: false }"
                         x-show="!eliminando"
                         class="bg-white rounded-xl border border-gray-100 shadow-sm flex items-stretch overflow-hidden
                                {{ $vencido ? 'opacity-60' : '' }}">

                        {{-- Fecha badge --}}
                        <div class="flex flex-col items-center justify-center w-16 shrink-0 px-2 py-3
                                    {{ $esHoy ? 'bg-[#fc5648] text-white' : ($vencido ? 'bg-gray-100 text-gray-400' : 'bg-gray-50 text-gray-700') }}">
                            @if($panorama->fecha)
                                <span class="text-[10px] font-bold uppercase leading-none opacity-70">
                                    {{ $panorama->fecha->translatedFormat('M') }}
                                </span>
                                <span class="text-2xl font-black leading-tight">
                                    {{ $panorama->fecha->format('j') }}
                                </span>
                                @if($panorama->fecha_fin && !$panorama->fecha->isSameDay($panorama->fecha_fin))
                                <span class="text-[9px] opacity-60 leading-none mt-0.5">
                                    → {{ $panorama->fecha_fin->format('j') }} {{ $panorama->fecha_fin->translatedFormat('M') }}
                                </span>
                                @endif
                            @else
                                <span class="text-gray-300 text-2xl">—</span>
                            @endif
                        </div>

                        {{-- Imagen --}}
                        <div class="shrink-0 w-16 self-stretch">
                            @if($panorama->imagen)
                                <img src="{{ asset('storage/' . $panorama->imagen) }}"
                                     alt="{{ $panorama->titulo }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-300 text-xl">📷</div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 px-4 py-3 min-w-0">
                            <p class="font-semibold text-gray-800 leading-snug truncate">{{ $panorama->titulo }}</p>
                            <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                @if($panorama->hora)
                                <span class="text-[11px] text-gray-500">🕐 {{ $panorama->hora }}</span>
                                @endif
                                @if($panorama->ubicacion)
                                <span class="text-[11px] text-gray-400 truncate max-w-[180px]">📍 {{ $panorama->ubicacion }}</span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-1.5 mt-1.5">
                                @if($cat)
                                <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                    {{ $cat['emoji'] }} {{ $cat['label'] }}
                                </span>
                                @endif
                                @if(!empty($panorama->dias_semana))
                                <span class="text-[10px] font-bold bg-purple-50 text-purple-700 px-2 py-0.5 rounded-full">
                                    🔁 {{ implode(' · ', array_map(fn($d) => \App\Models\Panorama::DIAS[$d] ?? $d, $panorama->dias_semana)) }}
                                </span>
                                @endif
                                @if($panorama->es_gratuito)
                                <span class="text-[10px] font-bold bg-green-50 text-green-700 px-2 py-0.5 rounded-full">🎟️ Gratis</span>
                                @endif
                                @if($panorama->enlace)
                                <span class="text-[10px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">🔗 Enlace</span>
                                @endif
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="flex flex-col items-end justify-between px-4 py-3 shrink-0 gap-2">
                            <form action="{{ route('admin.panoramas.toggle', $panorama) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="text-[11px] font-bold px-2.5 py-1 rounded-full transition
                                               {{ $panorama->activo
                                                    ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                    : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}">
                                    {{ $panorama->activo ? '● Visible' : '○ Oculto' }}
                                </button>
                            </form>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.panoramas.edit', $panorama) }}"
                                   class="text-xs font-bold text-blue-600 hover:underline">Editar</a>
                                <button type="button"
                                        class="text-xs font-bold text-red-400 hover:underline"
                                        @click="if(confirm('¿Eliminar «{{ addslashes($panorama->titulo) }}»?')) {
                                            fetch('{{ route('admin.panoramas.destroy', $panorama) }}', {
                                                method: 'DELETE',
                                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                            }).then(r => { if(r.ok || r.status === 302) eliminando = true });
                                        }">Eliminar</button>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>
            </details>
            @endif
            @endforeach

            @endif

        </div>
    </div>
</x-app-layout>
