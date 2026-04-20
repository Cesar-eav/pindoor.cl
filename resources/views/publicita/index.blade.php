@extends('layouts.pindoor')

@section('title', 'Publicita tu negocio — Pindoor.cl')

@section('canonical', route('publicita.index'))

@section('bodyClass', 'bg-gray-50 text-gray-900 font-serif')

@section('content')

{{-- Hero --}}
<section class="bg-[#fc5648] text-white py-20 px-4 text-center">
    <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight">
        Ponle el <span class="underline decoration-white/50">pin</span> a tu negocio
    </h1>
    <p class="text-xl md:text-2xl text-white/85 max-w-2xl mx-auto mb-8">
        Aparece en el mapa de Valparaíso. Llega a miles de turistas y locales que buscan
        exactamente lo que tú ofreces.
    </p>
    <a href="#formulario"
       class="inline-block bg-white text-[#fc5648] font-bold px-8 py-4 rounded-2xl shadow-lg hover:bg-gray-100 transition text-lg">
        Quiero publicitar →
    </a>
</section>

{{-- Beneficios --}}
<section class="max-w-5xl mx-auto px-4 py-16">
    <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">¿Por qué Pindoor?</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100 text-center">
            <div class="text-5xl mb-4">📍</div>
            <h3 class="text-xl font-bold mb-2">Presencia en el mapa</h3>
            <p class="text-gray-500 text-sm leading-relaxed">
                Tu negocio aparece geolocalizados en el mapa interactivo de Valparaíso,
                donde los turistas buscan qué hacer y a dónde ir.
            </p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100 text-center">
            <div class="text-5xl mb-4">📸</div>
            <h3 class="text-xl font-bold mb-2">Galería de fotos</h3>
            <p class="text-gray-500 text-sm leading-relaxed">
                Muestra lo mejor de tu lugar con una galería de imágenes, horarios,
                oferta del día y enlace directo a tu Instagram o web.
            </p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100 text-center">
            <div class="text-5xl mb-4">🎯</div>
            <h3 class="text-xl font-bold mb-2">Audiencia calificada</h3>
            <p class="text-gray-500 text-sm leading-relaxed">
                Llegamos a personas que ya están en Valparaíso o planificando su visita,
                con intención real de conocer y consumir.
            </p>
        </div>
    </div>
</section>

{{-- Qué incluye --}}
<section class="bg-white py-16 px-4">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-10">¿Qué incluye tu perfil?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach([
                ['✅', 'Ficha con nombre, descripción y categoría'],
                ['✅', 'Galería de fotografías'],
                ['✅', 'Mapa con tu ubicación exacta'],
                ['✅', 'Horario de atención'],
                ['✅', 'Enlace a tu web o Instagram'],
                ['✅', 'Oferta o promoción del día'],
                ['✅', 'Badge destacado en el listado'],
                ['✅', 'Panel de administración propio'],
            ] as [$ico, $texto])
            <div class="flex items-start gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                <span class="text-xl">{{ $ico }}</span>
                <span class="text-gray-700 text-sm font-medium">{{ $texto }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Ejemplos reales --}}
@if(isset($atractivos) && $atractivos->count())
<section class="bg-gray-50 py-16 px-4">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl text-red-400 font-bold text-center mb-3">Así se ve tu local en Pindoor</h2>
        <p class="text-center text-gray-500 text-sm mb-10">Ejemplos reales de negocios publicados en nuestra plataforma.</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach($atractivos as $atractivo)
            <article class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col">
                <div class="relative">
                    <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}" class="block overflow-hidden">
                        @if($atractivo->imagenPrincipal)
                            <img src="{{ asset('storage/' . $atractivo->imagenPrincipal->ruta) }}"
                                 alt="{{ $atractivo->title }}"
                                 class="w-full h-52 object-cover hover:scale-105 transition-transform duration-500"/>
                        @else
                            <div class="w-full h-52 bg-gray-100 flex items-center justify-center text-4xl">📍</div>
                        @endif
                    </a>
                    @if($atractivo->categoria)
                    <span class="absolute top-3 left-3 bg-[#fc5648] text-white text-[9px] uppercase tracking-widest font-bold px-2.5 py-1 rounded-full">
                        {{ $atractivo->categoria->nombre }}
                    </span>
                    @endif
                    @if($atractivo->es_cliente && $atractivo->oferta_del_dia && $atractivo->oferta_activa)
                    <span class="absolute top-3 right-3 bg-amber-400 text-amber-900 text-[9px] uppercase font-bold px-2.5 py-1 rounded-full">
                        Oferta hoy
                    </span>
                    @endif
                    @if($atractivo->es_cliente && $atractivo->imagen_perfil)
                    <img src="{{ asset('storage/' . $atractivo->imagen_perfil) }}"
                         alt="Logo {{ $atractivo->title }}"
                         class="absolute bottom-3 right-3 w-10 h-10 rounded-xl object-cover border-2 border-white shadow">
                    @endif
                </div>

                <div class="p-4 flex-grow">
                    <h3 class="text-base font-bold text-gray-900 mb-1 leading-tight">
                        <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}"
                           class="hover:text-[#fc5648] transition">{{ $atractivo->title }}</a>
                    </h3>
                    @if($atractivo->sector || $atractivo->direccion)
                    <div class="flex items-center gap-1.5 text-xs mb-2 flex-wrap">
                        @if($atractivo->sector)
                        <span class="flex items-center gap-1 text-[#fc5648] font-semibold">
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $atractivo->sector }}
                        </span>
                        @endif
                        @if($atractivo->sector && $atractivo->direccion)
                        <span class="text-gray-300">·</span>
                        @endif
                        @if($atractivo->direccion)
                        <span class="text-gray-400">{{ $atractivo->direccion }}</span>
                        @endif
                    </div>
                    @endif
                    <p class="text-gray-500 text-sm leading-relaxed">
                        {!! \Illuminate\Support\Str::limit(strip_tags($atractivo->description), 100) !!}
                    </p>
                </div>
            </article>
            @endforeach
        </div>


    </div>
</section>
@endif

{{-- Formulario de contacto --}}
<section id="formulario" class="max-w-2xl mx-auto px-4 py-20">
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-10">
        <h2 class="text-3xl font-bold mb-2 text-center">¡Hablemos!</h2>
        <p class="text-gray-500 text-center text-sm mb-8">
            Déjanos tus datos y te contactamos sin compromiso.
        </p>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-4 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('publicita.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Nombre <span class="text-[#fc5648]">*</span>
                </label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                       placeholder="Tu nombre"
                       class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#fc5648] transition text-sm {{ $errors->has('nombre') ? 'border-red-400' : 'border-gray-200' }}">
                @error('nombre')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Email <span class="text-[#fc5648]">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="tu@correo.com"
                           class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#fc5648] transition text-sm {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }}">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono</label>
                    <input type="tel" name="telefono" value="{{ old('telefono') }}"
                           placeholder="+56 9 1234 5678"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#fc5648] transition text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Nombre de tu negocio <span class="text-[#fc5648]">*</span>
                </label>
                <input type="text" name="negocio" value="{{ old('negocio') }}" required
                       placeholder="Café del Cerro, Hostal Los Artistas…"
                       class="w-full px-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#fc5648] transition text-sm {{ $errors->has('negocio') ? 'border-red-400' : 'border-gray-200' }}">
                @error('negocio')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mensaje (opcional)</label>
                <textarea name="mensaje" rows="4"
                          placeholder="Cuéntanos de tu negocio, qué ofreces, o cualquier pregunta que tengas…"
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#fc5648] transition text-sm resize-none">{{ old('mensaje') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full bg-[#fc5648] text-white font-bold py-3 rounded-2xl hover:bg-[#d94439] transition text-base shadow">
                Enviar mi consulta
            </button>
        </form>
    </div>
</section>

<footer class="text-center text-gray-400 text-xs py-8">
    © {{ date('Y') }} Pindoor.cl — El pionero de Valparaíso
</footer>

@endsection
