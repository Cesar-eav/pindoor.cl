@extends('layouts.pindoor')

@section('title', $operador->nombre . ' — Operador turístico en Pindoor')
@section('canonical', route('operador.show', $operador->slug))
@section('bodyClass', 'bg-gray-100 text-gray-900')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10 space-y-6">

    {{-- Cabecera / perfil --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Banner --}}
        <div class="h-28 w-full" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 60%, #115e59 100%)"></div>

        <div class="px-6 pb-6">
            {{-- Avatar + redes en fila --}}
            <div class="flex items-end justify-between gap-4 -mt-12 mb-4">
                <div>
                    @if($operador->imagen_perfil)
                        <img src="{{ asset('storage/' . $operador->imagen_perfil) }}"
                             alt="{{ $operador->nombre }}"
                             class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                    @else
                        <div class="w-24 h-24 rounded-full bg-teal-100 border-4 border-white shadow-md flex items-center justify-center text-4xl">🧭</div>
                    @endif
                </div>

                {{-- Redes sociales --}}
                <div class="flex items-center gap-2 flex-wrap justify-end pb-1">
                    @if($operador->enlace_whatsapp)
                        <a href="{{ $operador->enlace_whatsapp }}" target="_blank" rel="noopener" title="WhatsApp"
                           class="w-9 h-9 rounded-full flex items-center justify-center transition hover:scale-110"
                           style="background:#25D366">
                            <svg class="w-4.5 h-4.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    @endif
                    @if($operador->enlace_instagram)
                        <a href="{{ $operador->enlace_instagram }}" target="_blank" rel="noopener" title="Instagram"
                           class="w-9 h-9 rounded-full flex items-center justify-center transition hover:scale-110"
                           style="background:linear-gradient(135deg,#f9a825,#e91e63,#9c27b0)">
                            <svg class="w-4.5 h-4.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    @endif
                    @if($operador->enlace_facebook)
                        <a href="{{ $operador->enlace_facebook }}" target="_blank" rel="noopener" title="Facebook"
                           class="w-9 h-9 rounded-full flex items-center justify-center transition hover:scale-110"
                           style="background:#1877F2">
                            <svg class="w-4.5 h-4.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    @endif
                    @if($operador->enlace_web)
                        <a href="{{ $operador->enlace_web }}" target="_blank" rel="noopener" title="Sitio web"
                           class="w-9 h-9 rounded-full bg-gray-700 hover:bg-gray-900 flex items-center justify-center transition hover:scale-110">
                            <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900">{{ $operador->nombre }}</h1>
            <p class="text-sm font-semibold text-teal-600 mt-0.5">🧭 Operador turístico</p>
            @if($operador->ciudad)
                <p class="text-sm text-gray-500 mt-0.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    {{ $operador->ciudad }}
                </p>
            @endif

            @if($operador->descripcion)
                <p class="mt-4 text-gray-600 text-sm leading-relaxed whitespace-pre-line">{{ $operador->descripcion }}</p>
            @endif

            @if($operador->email_contacto || $operador->telefono)
                <div class="mt-5 pt-5 border-t border-gray-100 flex flex-wrap gap-3">
                    @if($operador->email_contacto)
                        <a href="mailto:{{ $operador->email_contacto }}"
                           class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
                            ✉️ Contactar por email
                        </a>
                    @endif
                    @if($operador->telefono)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $operador->telefono) }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
                            📱 WhatsApp
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Dónde puedes encontrarme --}}
    @if($operador->puntos->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-800 mb-4">¿A dónde te puede llevar?</h2>
        <div class="grid sm:grid-cols-2 gap-3">
            @foreach($operador->puntos as $punto)
            <a href="{{ route('puntos.show', $punto->slug) }}"
               class="flex items-center gap-3 bg-gray-50 hover:bg-teal-50 border border-gray-100 hover:border-teal-200 rounded-xl p-3 transition">
                @if($punto->imagenPrincipal)
                    <img src="{{ asset('storage/' . $punto->imagenPrincipal->ruta) }}" alt="{{ $punto->title }}"
                         class="w-10 h-10 rounded-lg object-cover shrink-0">
                @else
                    <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center text-lg shrink-0">
                        📍
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $punto->title }}</p>
                    @if($punto->categoria)
                        <p class="text-xs text-gray-400">{{ $punto->categoria->nombre }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
