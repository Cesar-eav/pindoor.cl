@extends('layouts.pindoor')

@section('title', 'Contacto — Pindoor.cl')
@section('description', 'Contáctanos para registrar tu local en Valparaíso o para unirte como artista o agrupación cultural a la red Pindoor.')
@section('canonical', route('contacto.index'))
@section('bodyClass', 'bg-gray-50 text-gray-900 font-sans antialiased')

@section('content')

@php $tabInicial = old('tipo', 'cliente'); @endphp

<div class="min-h-screen">

    {{-- HERO --}}
    <header class="bg-gray-900 text-white px-6 py-14 text-center"
            style="background: linear-gradient(135deg, #1a1c1e 0%, #000000 100%)">
        <div class="max-w-xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-white/70 mb-5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#fc5648] animate-pulse inline-block"></span>
                Conecta con Pindoor
            </div>
            <h1 class="text-4xl md:text-5xl font-black leading-tight mb-4">
                ¿Tienes un <span class="text-[#fc5648]">local</span>, eres <span class="text-[#fc5648]">artista</span><br>o formas parte de una <span class="text-[#fc5648]">agrupación cultural</span>?
            </h1>
            <p class="text-white/60 text-base md:text-lg mb-8">
                Cuéntanos quién eres y te contactamos para mostrarte cómo crecer en la red Pindoor.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="https://wa.me/56930821653" target="_blank" rel="noopener"
                   class="flex items-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-bold px-6 py-3.5 rounded-2xl shadow-lg transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.117.549 4.103 1.508 5.829L.057 23.25c-.079.326.235.64.561.561l5.422-1.451A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.806 9.806 0 01-5.028-1.38l-.36-.214-3.732.999 1-3.63-.234-.374A9.818 9.818 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                    </svg>
                    Escríbenos por WhatsApp
                </a>
                <a href="{{ route('register') }}"
                   class="flex items-center gap-2 bg-[#fc5648] hover:bg-[#ff6b5b] text-white font-bold px-6 py-3.5 rounded-2xl shadow-lg transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 text-sm">
                    Registro local →
                </a>
                <a href="{{ route('artista.register') }}"
                   class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-bold px-6 py-3.5 rounded-2xl shadow-lg transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 text-sm border border-white/20">
                    Registro artista / agrupación →
                </a>
            </div>
            <p class="text-white text-md mt-4">O completa el formulario y te contactamos nosotros.</p>
            <p class="text-white text-md mt-2">
                ¿Ya eres parte de Pindoor?
                <a href="{{ route('login') }}" class="text-white/70 font-bold hover:text-white underline underline-offset-2 transition">Inicia sesión</a>
            </p>
        </div>
    </header>

    @if(isset($atractivos) && $atractivos->count())
    <section class="py-10 px-4 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                <h2 class="text-2xl font-black text-gray-900">Así se ve tu espacio en Pindoor</h2>
                <p class="text-gray-500 text-sm mt-1">Hostal · Cafetería · Restaurante · Centro Cultural · Tienda</p>
            </div>

            {{-- Móvil: scroll horizontal --}}
            <div class="md:hidden flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 no-scrollbar">
                @foreach($atractivos->take(5) as $atractivo)
                <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}"
                   class="flex-none w-32 bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                    @if($atractivo->imagenPrincipal)
                        <img src="{{ asset('storage/' . $atractivo->imagenPrincipal->ruta) }}"
                             alt="{{ $atractivo->title }}" class="w-full h-24 object-cover">
                    @else
                        <div class="w-full h-24 bg-gray-100 flex items-center justify-center text-2xl">📍</div>
                    @endif
                    <div class="p-2">
                        <h3 class="text-xs font-bold leading-tight line-clamp-2">{{ $atractivo->title }}</h3>
                        @if($atractivo->sector)
                        <p class="text-[10px] text-[#fc5648] mt-0.5">{{ $atractivo->sector }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Desktop: grid compacta --}}
            <div class="hidden md:grid md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach($atractivos->take(5) as $atractivo)
                <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}"
                   class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group">
                    @if($atractivo->imagenPrincipal)
                        <img src="{{ asset('storage/' . $atractivo->imagenPrincipal->ruta) }}"
                             alt="{{ $atractivo->title }}"
                             class="w-full h-36 object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-36 bg-gray-100 flex items-center justify-center text-4xl">📍</div>
                    @endif
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 text-sm leading-tight">{{ $atractivo->title }}</h3>
                        @if($atractivo->sector)
                        <p class="text-xs text-[#fc5648] font-semibold mt-1">{{ $atractivo->sector }}</p>
                        @endif
                        @if($atractivo->direccion)
                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $atractivo->direccion }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <main class="max-w-2xl mx-auto px-4 py-10">

        {{-- Éxito --}}
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-5 py-4 flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Selector de tipo --}}
        <div class="flex gap-3 mb-8 bg-white rounded-2xl p-1.5 border border-gray-100 shadow-sm">
            <button type="button" id="btn-cliente" onclick="switchTab('cliente')"
                    class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl font-bold text-sm transition-all duration-200
                           {{ $tabInicial === 'cliente' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-500 hover:text-gray-800' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Tengo un local
            </button>
            <button type="button" id="btn-artista" onclick="switchTab('artista')"
                    class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl font-bold text-sm transition-all duration-200
                           {{ $tabInicial === 'artista' ? 'bg-[#fc5648] text-white shadow-md' : 'text-gray-500 hover:text-gray-800' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Soy artista / agrupación
            </button>
        </div>

        {{-- FORMULARIO CLIENTES --}}
        <div id="panel-cliente" class="{{ $tabInicial !== 'cliente' ? 'hidden' : '' }}">

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="bg-gray-900 px-6 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-sm">Registra tu local</h2>
                        <p class="text-white/50 text-xs">Hostal · Cafetería · Restaurante · Museo · Bar</p>
                    </div>
                </div>

                <form action="{{ route('contacto.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="tipo" value="cliente">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Tu nombre *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}"
                                   placeholder="Juan Pérez"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition @error('nombre') border-red-400 bg-red-50 @enderror">
                            @error('nombre') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="hola@milocal.cl"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition @error('email') border-red-400 bg-red-50 @enderror">
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Teléfono</label>
                            <input type="tel" name="telefono" value="{{ old('telefono') }}"
                                   placeholder="+56 9 1234 5678"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Tipo de local *</label>
                            <select name="tipo_local"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition @error('tipo_local') border-red-400 bg-red-50 @enderror">
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

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Nombre del local *</label>
                            <input type="text" name="nombre_local" value="{{ old('nombre_local') }}"
                                   placeholder="Hostal Vista al Mar"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition @error('nombre_local') border-red-400 bg-red-50 @enderror">
                            @error('nombre_local') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Ciudad / Comuna</label>
                            <input type="text" name="ciudad" value="{{ old('ciudad') }}"
                                   placeholder="Valparaíso, Viña del Mar…"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Cuéntanos algo más</label>
                            <textarea name="mensaje" rows="3" placeholder="¿Tienes alguna duda o quieres contarnos algo sobre tu espacio?"
                                      class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-none">{{ old('mensaje') }}</textarea>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-gray-900 hover:bg-black text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 text-sm tracking-wide">
                        Enviar solicitud →
                    </button>

                </form>
            </div>

            <div class="grid grid-cols-3 gap-3">
                @foreach([
                    ['📍', 'En el mapa', 'Geolocalización exacta para turistas y locales.'],
                    ['📸', 'Galería', 'Muestra fotos, horarios y ofertas del día.'],
                    ['🎯', 'Gratis', 'Sin costo para empezar. Sin letra chica.'],
                ] as [$ico, $t, $d])
                <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                    <span class="text-2xl">{{ $ico }}</span>
                    <p class="text-xs font-bold text-gray-800 mt-1">{{ $t }}</p>
                    <p class="text-[10px] text-gray-400 leading-tight mt-0.5">{{ $d }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- FORMULARIO ARTISTAS --}}
        <div id="panel-artista" class="{{ $tabInicial !== 'artista' ? 'hidden' : '' }}">

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 flex items-center gap-3"
                     style="background: linear-gradient(135deg, #fc5648 0%, #d94035 100%)">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-sm">Únete como artista o agrupación cultural</h2>
                        <p class="text-white/70 text-xs">Músico · Pintor · Actor · Fotógrafo · Compañía · Colectivo</p>
                    </div>
                </div>

                <form action="{{ route('contacto.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="tipo" value="artista">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Tu nombre artístico *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}"
                                   placeholder="Ana Torres"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:border-transparent transition @error('nombre') border-red-400 bg-red-50 @enderror">
                            @error('nombre') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="hola@artista.cl"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:border-transparent transition @error('email') border-red-400 bg-red-50 @enderror">
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Teléfono</label>
                            <input type="tel" name="telefono" value="{{ old('telefono') }}"
                                   placeholder="+56 9 1234 5678"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:border-transparent transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Disciplina artística *</label>
                            <select name="especialidad"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:border-transparent transition @error('especialidad') border-red-400 bg-red-50 @enderror">
                                <option value="">Seleccionar…</option>
                                @foreach([
                                    'musica'         => 'Música',
                                    'artes_visuales' => 'Artes Visuales / Pintura',
                                    'fotografia'     => 'Fotografía',
                                    'teatro'         => 'Teatro / Actuación',
                                    'danza'          => 'Danza / Baile',
                                    'ceramica'       => 'Cerámica / Escultura',
                                    'literatura'     => 'Literatura / Escritura',
                                    'circo'          => 'Circo / Acrobacia',
                                    'diseño'         => 'Diseño / Ilustración',
                                    'artesania'      => 'Artesanía',
                                    'audiovisual'    => 'Cine / Audiovisual',
                                    'otro'           => 'Otro',
                                ] as $val => $label)
                                <option value="{{ $val }}" {{ old('especialidad') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('especialidad') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Ciudad</label>
                            <input type="text" name="ciudad" value="{{ old('ciudad') }}"
                                   placeholder="Valparaíso, Santiago…"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:border-transparent transition">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Cuéntanos sobre tu trabajo</label>
                            <textarea name="mensaje" rows="4" placeholder="¿Qué tipo de obras realizas? ¿Dónde has expuesto o tocado? Cuéntanos lo que quieras."
                                      class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:border-transparent transition resize-none">{{ old('mensaje') }}</textarea>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 text-sm tracking-wide text-white"
                            style="background: linear-gradient(135deg, #fc5648 0%, #d94035 100%)">
                        Enviar solicitud →
                    </button>

                    <div class="flex items-center gap-3 text-gray-300 text-xs">
                        <span class="flex-1 border-t border-gray-200"></span>
                        o
                        <span class="flex-1 border-t border-gray-200"></span>
                    </div>

                    <a href="https://wa.me/56930821653" target="_blank" rel="noopener"
                       class="flex items-center justify-center gap-2 w-full bg-[#25D366] hover:bg-[#1ebe5d] text-white font-bold py-3.5 rounded-xl transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 text-sm shadow-md">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.117.549 4.103 1.508 5.829L.057 23.25c-.079.326.235.64.561.561l5.422-1.451A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.806 9.806 0 01-5.028-1.38l-.36-.214-3.732.999 1-3.63-.234-.374A9.818 9.818 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                        </svg>
                        Escríbenos por WhatsApp
                    </a>

                    <p class="text-center text-xs text-gray-400">
                        ¿No quieres esperar?
                        <a href="{{ route('artista.register') }}" class="font-bold text-[#fc5648] hover:underline">¡Regístrate ahora!</a>
                    </p>
                </form>
            </div>

            <div class="grid grid-cols-3 gap-3">
                @foreach([
                    ['🎨', 'Tu vitrina', 'Perfil público con galería de tu obra.'],
                    ['📍', 'En el mapa', 'Aparece en búsquedas de turistas y locales.'],
                    ['🤝', 'Red local', 'Conecta con espacios que buscan artistas.'],
                ] as [$ico, $t, $d])
                <div class="bg-white rounded-xl border border-gray-100 p-3 text-center shadow-sm">
                    <span class="text-2xl">{{ $ico }}</span>
                    <p class="text-xs font-bold text-gray-800 mt-1">{{ $t }}</p>
                    <p class="text-[10px] text-gray-400 leading-tight mt-0.5">{{ $d }}</p>
                </div>
                @endforeach
            </div>
        </div>

    </main>
</div>

{{-- Botón WhatsApp fijo (móvil) --}}
<a href="https://wa.me/56930821653"
   target="_blank" rel="noopener"
   class="md:hidden fixed bottom-6 right-5 z-50 flex items-center gap-2 bg-[#25D366] text-white font-bold text-sm px-4 py-3 rounded-full shadow-xl active:scale-95 transition-transform">
    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.117.549 4.103 1.508 5.829L.057 23.25c-.079.326.235.64.561.561l5.422-1.451A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.806 9.806 0 01-5.028-1.38l-.36-.214-3.732.999 1-3.63-.234-.374A9.818 9.818 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
    </svg>
    WhatsApp
</a>


@section('scripts')
<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
<script>
function switchTab(tab) {
    const panels  = { cliente: document.getElementById('panel-cliente'),  artista: document.getElementById('panel-artista') };
    const buttons = { cliente: document.getElementById('btn-cliente'),    artista: document.getElementById('btn-artista') };

    panels.cliente.classList.toggle('hidden', tab !== 'cliente');
    panels.artista.classList.toggle('hidden', tab !== 'artista');

    buttons.cliente.className = buttons.cliente.className
        .replace(/bg-gray-900 text-white shadow-md|text-gray-500 hover:text-gray-800/g, '').trim()
        + (tab === 'cliente' ? ' bg-gray-900 text-white shadow-md' : ' text-gray-500 hover:text-gray-800');

    buttons.artista.className = buttons.artista.className
        .replace(/bg-\[#fc5648\] text-white shadow-md|text-gray-500 hover:text-gray-800/g, '').trim()
        + (tab === 'artista' ? ' bg-[#fc5648] text-white shadow-md' : ' text-gray-500 hover:text-gray-800');
}
</script>
@endsection

@endsection
