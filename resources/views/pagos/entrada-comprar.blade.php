@extends('layouts.pindoor')

@php
    $titulo = $item->datos['titulo'] ?? 'Evento';
    $tipoInfo = $item->tipoEvento();
@endphp

@section('title', 'Comprar entradas · ' . $titulo . ' · Pindoor')
@section('bodyClass', 'bg-[#f9fafb] text-gray-900')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10"
     x-data="entradaWidget({
         precio: {{ (int) $precio }},
         cupoDisponible: {{ $cupoDisponible === null ? 'null' : (int) $cupoDisponible }},
     })">

    <nav class="flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-gray-400 mb-6">
        <a href="{{ route('puntos.evento', ['slug' => $item->punto->slug, 'item' => $item->id]) }}" class="hover:text-[#fc5648] transition truncate max-w-55">{{ $titulo }}</a>
        <span class="text-gray-300">/</span>
        <span class="text-[#fc5648]">Comprar entradas</span>
    </nav>

    @if($item->imagen)
    <div class="aspect-video rounded-3xl overflow-hidden shadow-sm mb-6 bg-gray-100">
        <img src="{{ asset('storage/' . $item->imagen) }}" alt="{{ $titulo }}" class="w-full h-full object-cover">
    </div>
    @endif

    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-3">{{ $titulo }}</h1>

    <div class="flex flex-wrap items-center gap-2 mb-8">
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#fff0ef] text-[#fc5648] text-xs font-bold">
            {{ $tipoInfo['emoji'] }} {{ $tipoInfo['label'] }}
        </span>
        <span class="text-sm text-gray-500">
            📅 {{ $item->fecha->translatedFormat('d \d\e F Y') }}
            @if($item->datos['hora'] ?? null) · 🕐 {{ \Carbon\Carbon::parse($item->datos['hora'])->format('H:i') }} @endif
        </span>
        @if($item->punto)
            <span class="text-sm text-gray-500">· {{ $item->punto->title }}</span>
        @endif
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    @if($cupoDisponible === 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="text-4xl mb-3">😕</div>
            <p class="font-bold text-gray-800 mb-1">Entradas agotadas</p>
            <p class="text-sm text-gray-500">Ya no quedan entradas disponibles para este evento.</p>
        </div>
    @else
    <form action="{{ route('entradas.comprar', $item->id) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <label class="block text-sm font-bold text-gray-700 mb-3">¿Cuántas entradas?</label>

            @if($cupoDisponible !== null)
            <p class="text-xs text-gray-400 mb-3">Quedan {{ $cupoDisponible }} entrada{{ $cupoDisponible === 1 ? '' : 's' }} disponible{{ $cupoDisponible === 1 ? '' : 's' }}.</p>
            @endif

            <div class="flex items-center gap-3">
                <button type="button" @click="cantidad = Math.max(1, cantidad - 1)"
                        class="w-10 h-10 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition">−</button>
                <input type="number" name="cantidad_entradas" x-model.number="cantidad" min="1" :max="maxCantidad()"
                       class="w-20 text-center px-2 py-2.5 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-[#fc5648] outline-none">
                <button type="button" @click="cantidad = Math.min(maxCantidad(), cantidad + 1)"
                        class="w-10 h-10 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition">+</button>
            </div>

            <template x-if="excedeCupo()">
                <p class="text-xs text-amber-600 font-semibold mt-3">
                    Solo quedan <span x-text="cupoDisponible"></span> entradas disponibles. Ajusta la cantidad.
                </p>
            </template>

            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm font-bold text-gray-700">Total a pagar</span>
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

        <button type="submit" :disabled="excedeCupo()"
                :class="excedeCupo() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#e64536]'"
                class="w-full bg-[#fc5648] text-white font-extrabold py-3.5 rounded-2xl transition">
            Ir a pagar con Flow
        </button>
    </form>
    @endif
</div>
@endsection

@section('scripts')
<script>
function entradaWidget(config) {
    return {
        cantidad: 1,
        precio: config.precio,
        cupoDisponible: config.cupoDisponible,
        maxCantidad() {
            return this.cupoDisponible === null ? 20 : Math.max(1, this.cupoDisponible);
        },
        precioTotal() {
            return Math.max(1, this.cantidad || 0) * this.precio;
        },
        excedeCupo() {
            if (this.cupoDisponible === null) return false;
            return (this.cantidad || 0) > this.cupoDisponible;
        },
    };
}
</script>
@endsection
