@extends('layouts.pindoor')

@section('title', 'Reservar ' . $ruta->titulo . ' con ' . $operador->nombre . ' · Pindoor')
@section('bodyClass', 'bg-[#f9fafb] text-gray-900')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10"
     x-data="reservaWidget({
         disponibilidadUrl: @js(route('rutas.reservar.disponibilidad', [$ruta->slug, $operador->slug])),
         precioIndividual: {{ (int) $rutaOperador->precio_individual }},
         precioGrupoAdulto: {{ (int) $rutaOperador->precio_grupo_adulto }},
         precioNino: {{ (int) $rutaOperador->precio_nino }},
         hoy: @js(now()->format('Y-m-d')),
     })">

    <nav class="flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-gray-400 mb-6">
        <a href="{{ route('rutas.show', $ruta->slug) }}" class="hover:text-[#fc5648] transition">{{ $ruta->titulo }}</a>
        <span class="text-gray-300">/</span>
        <span class="text-[#fc5648]">Reservar</span>
    </nav>

    @if($ruta->imagen_portada_url)
    <div class="aspect-video rounded-3xl overflow-hidden shadow-sm mb-6 bg-gray-100">
        <img src="{{ $ruta->imagen_portada_url }}" alt="{{ $ruta->titulo }}" class="w-full h-full object-cover">
    </div>
    @endif

    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-3">{{ $ruta->titulo }}</h1>

    <div class="flex items-center gap-3 mb-8">
        <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-100 shrink-0 border border-gray-200">
            @if($operador->imagen_perfil)
                <img src="{{ asset('storage/' . $operador->imagen_perfil) }}" alt="{{ $operador->nombre }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-lg text-gray-300">🧭</div>
            @endif
        </div>
        <p class="text-sm text-gray-500">Operado por <span class="font-bold text-gray-800">{{ $operador->nombre }}</span></p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('rutas.reservar.store', [$ruta->slug, $operador->slug]) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <label class="block text-sm font-bold text-gray-700 mb-2">Fecha de visita</label>
            <input type="date" name="fecha_visita" x-model="fecha" @change="buscarDisponibilidad()"
                   :min="hoy" required value="{{ old('fecha_visita') }}"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">

            <template x-if="fecha && cargando">
                <p class="text-xs text-gray-400 mt-3">Buscando horarios disponibles…</p>
            </template>

            <template x-if="fecha && !cargando && horariosDisponibles.length === 0">
                <p class="text-xs text-amber-600 mt-3">No hay horarios disponibles para esa fecha. Prueba con otro día.</p>
            </template>

            <div class="mt-4 space-y-2" x-show="horariosDisponibles.length > 0">
                <label class="block text-sm font-bold text-gray-700 mb-1">Horario</label>
                <template x-for="h in horariosDisponibles" :key="h.id">
                    <label class="flex items-center justify-between border border-gray-200 rounded-xl px-4 py-2.5 cursor-pointer"
                           :class="horarioId == h.id ? 'border-[#fc5648] ring-1 ring-[#fc5648]' : ''">
                        <span class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <input type="radio" name="horario_id" :value="h.id" x-model="horarioId" required
                                   class="text-[#fc5648] focus:ring-[#fc5648]">
                            <span x-text="h.hora"></span>
                        </span>
                        <span class="text-xs text-gray-400" x-text="h.cupo_disponible + ' cupos disponibles'"></span>
                    </label>
                </template>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <label class="block text-sm font-bold text-gray-700 mb-3">¿Cuántos van?</label>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Adultos</label>
                    <input type="number" name="cantidad_adultos" x-model.number="adultos" min="1" max="30" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Niños</label>
                    <input type="number" name="cantidad_ninos" x-model.number="ninos" min="0" max="30"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm font-bold text-gray-700">Total estimado</span>
                <span class="text-xl font-extrabold text-[#fc5648]" x-text="'$' + precioTotal().toLocaleString('es-CL')"></span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
            <label class="block text-sm font-bold text-gray-700">Tus datos</label>
            <input type="text" name="nombre_cliente" placeholder="Nombre completo" required value="{{ old('nombre_cliente') }}"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
            <input type="email" name="email_cliente" placeholder="Email" required value="{{ old('email_cliente') }}"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
            <input type="text" name="telefono_cliente" placeholder="Teléfono" required value="{{ old('telefono_cliente') }}"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        </div>

        <button type="submit" :disabled="!horarioId"
                :class="!horarioId ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#e64536]'"
                class="w-full bg-[#fc5648] text-white font-extrabold py-3.5 rounded-2xl transition">
            Ir a pagar con Flow
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
function reservaWidget(config) {
    return {
        fecha: '',
        hoy: config.hoy,
        cargando: false,
        horariosDisponibles: [],
        horarioId: null,
        adultos: 1,
        ninos: 0,
        buscarDisponibilidad() {
            this.horarioId = null;
            this.horariosDisponibles = [];
            if (!this.fecha) return;
            this.cargando = true;
            fetch(config.disponibilidadUrl + '?fecha=' + this.fecha)
                .then(r => r.json())
                .then(data => { this.horariosDisponibles = data.horarios || []; })
                .finally(() => { this.cargando = false; });
        },
        precioTotal() {
            const adultos = Math.max(1, this.adultos || 0);
            const ninos = Math.max(0, this.ninos || 0);
            if (adultos === 1 && ninos === 0) {
                return config.precioIndividual;
            }
            return adultos * config.precioGrupoAdulto + ninos * config.precioNino;
        },
    };
}
</script>
@endsection

