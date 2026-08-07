@extends('layouts.pindoor')

@section('title', 'Contacto — Pindoor.cl')
@section('description', 'Escríbenos por dudas, sugerencias o cualquier consulta sobre Pindoor.')
@section('canonical', route('contacto.index'))
@section('bodyClass', 'bg-gray-50 text-gray-900 font-sans antialiased')

@section('content')

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
                ¿Tienes una <span class="text-[#fc5648]">consulta</span>?
            </h1>
            <p class="text-white/60 text-base md:text-lg mb-8">
                Escríbenos y te respondemos a la brevedad.
            </p>

            <a href="https://wa.me/56930821653" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-bold px-6 py-3.5 rounded-2xl shadow-lg transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 text-sm">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.117.549 4.103 1.508 5.829L.057 23.25c-.079.326.235.64.561.561l5.422-1.451A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.806 9.806 0 01-5.028-1.38l-.36-.214-3.732.999 1-3.63-.234-.374A9.818 9.818 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                </svg>
                Escríbenos por WhatsApp
            </a>
            <p class="text-white text-md mt-4">O completa el formulario y te contactamos nosotros.</p>
        </div>
    </header>

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

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-white font-bold text-sm">Envíanos un mensaje</h2>
                    <p class="text-white/50 text-xs">Te respondemos por email o WhatsApp</p>
                </div>
            </div>

            <form action="{{ route('contacto.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

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
                               placeholder="hola@correo.cl"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition @error('email') border-red-400 bg-red-50 @enderror">
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Teléfono</label>
                        <input type="tel" name="telefono" value="{{ old('telefono') }}"
                               placeholder="+56 9 1234 5678"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Mensaje</label>
                        <textarea name="mensaje" rows="4" placeholder="Cuéntanos en qué te podemos ayudar"
                                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-none">{{ old('mensaje') }}</textarea>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-gray-900 hover:bg-black text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 text-sm tracking-wide">
                    Enviar mensaje →
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            ¿Quieres registrar tu negocio o unirte como artista?
            <a href="{{ route('publicita.index') }}" class="font-bold text-[#fc5648] hover:underline">Ingresa aquí →</a>
        </p>
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

@endsection
