@extends('layouts.pindoor')

@section('title', 'Activa tu perfil — ' . $punto->title . ' — Pindoor')
@section('description', 'Activa gratuitamente el perfil de ' . $punto->title . ' en Pindoor para agregar fotos, menú, eventos y promociones.')
@section('bodyClass', 'bg-gray-50 text-gray-900')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10">

    <a href="{{ route('puntos.show', $punto->slug) }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 mb-5 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Volver al perfil
    </a>

    <div class="text-center mb-8">
        <span class="text-4xl">✨</span>
        <h1 class="text-2xl font-extrabold text-gray-900 mt-2">Activa tu perfil en Pindoor</h1>
        <p class="text-sm text-gray-500 mt-2 leading-relaxed">
            Cuéntanos que eres el propietario de <strong>{{ $punto->title }}</strong> y te ayudamos a activar tu perfil gratis
            para agregar fotos, menú, eventos, promociones y mantener tu información siempre actualizada.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('contacto.store') }}" method="POST" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
        @csrf
        <input type="hidden" name="tipo" value="cliente">
        <input type="hidden" name="nombre_local" value="{{ $punto->title }}">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Tu nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}"
                       placeholder="Juan Pérez"
                       class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:border-transparent transition @error('nombre') border-red-400 bg-red-50 @enderror">
                @error('nombre') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="hola@minegocio.cl"
                       class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:border-transparent transition @error('email') border-red-400 bg-red-50 @enderror">
                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Teléfono</label>
                <input type="tel" name="telefono" value="{{ old('telefono') }}"
                       placeholder="+56 9 1234 5678"
                       class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:border-transparent transition">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Tipo de negocio *</label>
                <select name="tipo_local"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:border-transparent transition @error('tipo_local') border-red-400 bg-red-50 @enderror">
                    <option value="">Seleccionar…</option>
                    @foreach([
                        'hostal'      => 'Hostal / Hotel',
                        'cafeteria'   => 'Cafetería',
                        'restaurante' => 'Restaurante',
                        'bar'         => 'Bar / Pub',
                        'museo'       => 'Museo / Centro Cultural',
                        'galeria'     => 'Galería de Arte',
                        'tienda'      => 'Tienda / Boutique',
                        'spa'         => 'Spa / Bienestar',
                        'tour'        => 'Agencia de Tours',
                        'otro'        => 'Otro',
                    ] as $val => $label)
                    <option value="{{ $val }}" {{ old('tipo_local') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('tipo_local') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Mensaje</label>
                <textarea name="mensaje" rows="3"
                          class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:border-transparent transition resize-none">{{ old('mensaje', 'Quiero activar el perfil de ' . $punto->title . ' (' . route('puntos.show', $punto->slug) . ').') }}</textarea>
            </div>
        </div>

        <button type="submit"
                class="w-full bg-[#fc5648] hover:bg-[#d94439] text-white font-bold py-4 rounded-xl shadow-lg transition text-sm">
            Enviar solicitud →
        </button>
    </form>
</div>
@endsection
