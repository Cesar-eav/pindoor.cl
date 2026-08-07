<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi perfil de artista / agrupación</h2>
            <a href="{{ route('artista.show', $artista->slug) }}" target="_blank"
               class="text-xs font-bold text-violet-600 hover:underline flex items-center gap-1">
                Ver perfil público
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ tab: '{{ $errors->any() ? 'perfil' : 'perfil' }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{-- Pestañas --}}
            <div class="flex gap-1 mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5 w-fit">
                <button type="button" @click="tab = 'perfil'"
                        :class="tab === 'perfil'
                            ? 'bg-violet-600 text-white shadow-sm'
                            : 'text-gray-500 hover:text-gray-800'"
                        class="flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Perfil
                </button>
                <button type="button" @click="tab = 'galeria'"
                        :class="tab === 'galeria'
                            ? 'bg-violet-600 text-white shadow-sm'
                            : 'text-gray-500 hover:text-gray-800'"
                        class="flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Galería
                    @if($artista->imagenes->isNotEmpty())
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                              :class="tab === 'galeria' ? 'bg-white/20 text-white' : 'bg-violet-100 text-violet-600'">
                            {{ $artista->imagenes->count() }}
                        </span>
                    @endif
                </button>
                <button type="button" @click="tab = 'eventos'"
                        :class="tab === 'eventos'
                            ? 'bg-violet-600 text-white shadow-sm'
                            : 'text-gray-500 hover:text-gray-800'"
                        class="flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Eventos
                    @if($eventos->isNotEmpty())
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                              :class="tab === 'eventos' ? 'bg-white/20 text-white' : 'bg-violet-100 text-violet-600'">
                            {{ $eventos->count() }}
                        </span>
                    @endif
                </button>
            </div>

            {{-- Pestaña: Perfil --}}
            <div x-show="tab === 'perfil'" x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">

                <form action="{{ route('artista.perfil.actualizar') }}" method="POST"
                      enctype="multipart/form-data"
                      x-data="{ guardando: false }"
                      @submit="guardando = true">
                    @csrf @method('PUT')

                    <div class="grid lg:grid-cols-3 gap-6 items-stretch">

                        {{-- Identidad: 2 cols --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2 h-full">
                            <div class="flex flex-col lg:flex-row gap-6">

                                {{-- Avatar --}}
                                <div class="flex lg:flex-col items-center gap-4 lg:gap-3 lg:w-40 shrink-0">
                                    @if($artista->imagen_perfil)
                                        <img src="{{ asset('storage/' . $artista->imagen_perfil) }}"
                                             alt="{{ $artista->nombre }}"
                                             class="w-20 h-20 lg:w-36 lg:h-36 rounded-full object-cover border-4 border-violet-100 shadow-sm shrink-0">
                                    @else
                                        <div class="w-20 h-20 lg:w-36 lg:h-36 rounded-full bg-violet-100 flex items-center justify-center text-4xl lg:text-5xl border-4 border-violet-50 shrink-0">🎨</div>
                                    @endif
                                    <div class="flex-1 lg:w-full">
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">Foto de perfil</label>
                                        <input type="file" name="imagen" accept="image/*"
                                               class="block w-full text-xs text-gray-500
                                                      file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0
                                                      file:text-xs file:font-bold file:bg-violet-50 file:text-violet-700
                                                      hover:file:bg-violet-100 cursor-pointer">
                                        <p class="text-[10px] text-gray-400 mt-1">JPG, PNG — máx. 4 MB</p>
                                    </div>
                                </div>

                                {{-- Campos en grid 2 cols --}}
                                <div class="flex-1 grid sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre artístico o de la agrupación *</label>
                                        <input type="text" name="nombre" value="{{ old('nombre', $artista->nombre) }}" required
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Disciplina *</label>
                                        <select name="disciplina" required
                                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none bg-white">
                                            @foreach(\App\Models\Artista::DISCIPLINAS as $slug => $d)
                                                <option value="{{ $slug }}" {{ old('disciplina', $artista->disciplina) === $slug ? 'selected' : '' }}>
                                                    {{ $d['emoji'] }} {{ $d['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ciudad</label>
                                        <input type="text" name="ciudad" value="{{ old('ciudad', $artista->ciudad) }}"
                                               placeholder="Valparaíso…"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción / Biografía</label>
                                        <textarea name="descripcion" rows="7"
                                                  placeholder="Cuéntanos sobre tu trabajo, trayectoria y propuesta artística…"
                                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none resize-none">{{ old('descripcion', $artista->descripcion) }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono</label>
                                        <input type="text" name="telefono" value="{{ old('telefono', $artista->telefono) }}"
                                               placeholder="+56 9 1234 5678"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email de contacto público</label>
                                        <input type="email" name="email_contacto" value="{{ old('email_contacto', $artista->email_contacto) }}"
                                               placeholder="contacto@tumisma.cl"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Redes: col derecha con guardar al pie --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3 h-full">
                            <h3 class="font-bold text-gray-800">Redes y enlaces</h3>

                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                                    <span class="w-5 h-5 rounded flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#f9a825,#e91e63,#9c27b0)">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </span>
                                    Instagram
                                </label>
                                <input type="url" name="enlace_instagram" value="{{ old('enlace_instagram', $artista->enlace_instagram) }}"
                                       placeholder="https://instagram.com/tu_usuario"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                            </div>

                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                                    <span class="w-5 h-5 rounded flex items-center justify-center shrink-0" style="background:#1877F2">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </span>
                                    Facebook
                                </label>
                                <input type="url" name="enlace_facebook" value="{{ old('enlace_facebook', $artista->enlace_facebook) }}"
                                       placeholder="https://facebook.com/tu_pagina"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                            </div>

                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                                    <span class="w-5 h-5 rounded flex items-center justify-center shrink-0" style="background:#1DB954">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
                                    </span>
                                    Spotify
                                </label>
                                <input type="url" name="enlace_spotify" value="{{ old('enlace_spotify', $artista->enlace_spotify) }}"
                                       placeholder="https://open.spotify.com/artist/..."
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                            </div>

                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                                    <span class="w-5 h-5 rounded flex items-center justify-center shrink-0" style="background:#FF0000">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    </span>
                                    YouTube — Canal
                                </label>
                                <input type="url" name="enlace_youtube" value="{{ old('enlace_youtube', $artista->enlace_youtube) }}"
                                       placeholder="https://youtube.com/@tu_canal"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                            </div>

                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                                    <span class="w-5 h-5 rounded flex items-center justify-center shrink-0" style="background:#FF0000">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    </span>
                                    YouTube — Video de presentación
                                </label>
                                <input type="url" name="enlace_youtube_video" value="{{ old('enlace_youtube_video', $artista->enlace_youtube_video) }}"
                                       placeholder="https://youtube.com/watch?v=..."
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                            </div>

                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                                    <span class="w-5 h-5 rounded bg-gray-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    </span>
                                    Sitio web
                                </label>
                                <input type="url" name="enlace_web" value="{{ old('enlace_web', $artista->enlace_web) }}"
                                       placeholder="https://tu-sitio.cl"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                            </div>

                            <div class="pt-2 border-t border-gray-100 mt-auto">
                                <button type="submit"
                                        :disabled="guardando"
                                        class="w-full bg-violet-600 hover:bg-violet-700 disabled:opacity-70 text-white px-6 py-3 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                                    <svg x-show="guardando" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" style="display:none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    <span x-text="guardando ? 'Guardando…' : 'Guardar cambios'">Guardar cambios</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            {{-- Pestaña: Galería --}}
            <div x-show="tab === 'galeria'" x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                 style="display:none">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                    {{-- Imágenes existentes --}}
                    @if($artista->imagenes->isNotEmpty())
                        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3 mb-6">
                            @foreach($artista->imagenes as $img)
                                <div class="relative group" id="img-wrap-{{ $img->id }}">
                                    <img src="{{ asset('storage/' . $img->ruta) }}"
                                         alt="Portafolio"
                                         class="w-full aspect-square object-cover rounded-xl border border-gray-200">
                                    <button type="button"
                                            onclick="eliminarImagenArtista({{ $img->id }}, '{{ route('artista.imagen.eliminar', $img) }}', '{{ csrf_token() }}')"
                                            class="absolute top-1 right-1 bg-white/90 text-red-500 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shadow hover:bg-red-500 hover:text-white transition opacity-0 group-hover:opacity-100">
                                        ✕
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 3h18M3 21h18"/>
                            </svg>
                            <p class="text-sm font-medium">Aún no tienes imágenes en tu portafolio</p>
                            <p class="text-xs mt-1">Sube tus trabajos para mostrarlos en tu perfil público</p>
                        </div>
                    @endif

                    {{-- Upload --}}
                    <form action="{{ route('artista.imagen.subir') }}" method="POST"
                          enctype="multipart/form-data"
                          class="border-t border-gray-100 pt-6"
                          onsubmit="if(!this.querySelector('input[type=file]').files.length){ alert('Selecciona al menos una imagen antes de subir.'); return false; }">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Agregar imágenes al portafolio</label>
                                <div class="border-2 border-dashed border-gray-200 hover:border-violet-300 rounded-xl p-4 transition">
                                    <input type="file" name="imagenes[]" accept="image/*" multiple
                                           class="block w-full text-sm text-gray-500
                                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                                  file:text-xs file:font-bold file:bg-violet-50 file:text-violet-700
                                                  hover:file:bg-violet-100 cursor-pointer">
                                    <p class="text-xs text-gray-400 mt-2">JPG, PNG, WEBP — máx. 4 MB por imagen. Podés seleccionar varias a la vez.</p>
                                </div>
                            </div>
                            <button type="submit"
                                    class="shrink-0 bg-gray-800 text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-700 transition">
                                Subir imágenes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Pestaña: Eventos --}}
            <div x-show="tab === 'eventos'" x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                 style="display:none"
                 x-data="{
                    abierto: {{ $errors->any() ? 'true' : 'false' }},
                    editando: null,
                    imagenActual: null,
                    imagenPreview: null,
                    imagenError: null,
                    enviando: false,
                    resetForm() {
                        this.editando = null;
                        this.imagenActual = null;
                        this.imagenError = null;
                        this.enviando = false;
                        if (this.imagenPreview) URL.revokeObjectURL(this.imagenPreview);
                        this.imagenPreview = null;
                        this.$refs.form.reset();
                    },
                    previsualizar(input) {
                        this.imagenError = null;
                        if (this.imagenPreview) URL.revokeObjectURL(this.imagenPreview);
                        this.imagenPreview = null;
                        const file = input.files[0];
                        if (!file) return;

                        const MAX_MB = 20;
                        const TIPOS_OK = ['image/jpeg', 'image/png', 'image/webp'];

                        if (file.size > MAX_MB * 1024 * 1024) {
                            this.imagenError = `La imagen pesa ${(file.size / (1024 * 1024)).toFixed(1)} MB. El máximo permitido es ${MAX_MB} MB.`;
                            input.value = '';
                            return;
                        }
                        if (!TIPOS_OK.includes(file.type)) {
                            this.imagenError = 'Formato no permitido. Sube una imagen JPG, PNG o WEBP.';
                            input.value = '';
                            return;
                        }
                        try {
                            this.imagenPreview = URL.createObjectURL(file);
                        } catch (e) {
                            this.imagenError = 'No se pudo cargar la imagen. Intenta con otro archivo.';
                            input.value = '';
                        }
                    },
                    editar(evento) {
                        this.editando = evento;
                        this.imagenActual = evento.imagen_url;
                        this.imagenError = null;
                        if (this.imagenPreview) URL.revokeObjectURL(this.imagenPreview);
                        this.imagenPreview = null;
                        this.abierto = true;
                        this.$nextTick(() => {
                            const f = this.$refs.form;
                            f.titulo.value = evento.titulo;
                            f.tipo.value = evento.tipo;
                            f.fecha.value = evento.fecha;
                            f.hora.value = evento.hora || '';
                            f.hora_fin.value = evento.hora_fin || '';
                            f.precio.value = evento.precio || '';
                            f.precio_texto.value = evento.precio_texto || '';
                            f.descripcion.value = evento.descripcion || '';
                            f.url_entradas.value = evento.url_entradas || '';
                            f.destacado.checked = evento.destacado;
                        });
                    }
                }">

                {{-- Formulario nuevo / editar --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="font-bold text-gray-700" x-text="editando ? '✏️ Editar evento' : '📅 Programar evento'"></h4>
                            <p class="text-xs text-gray-400 mt-0.5">Conciertos, exposiciones, funciones, talleres y más.</p>
                        </div>
                        <button @click="abierto = !abierto; if (!abierto) resetForm()"
                                class="px-4 py-2 bg-violet-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition"
                                x-text="abierto ? 'Cerrar' : '+ Nuevo evento'"></button>
                    </div>

                    <div x-show="abierto" x-transition class="border-t border-gray-100 pt-5 mt-2">
                        <form method="POST" action="{{ route('artista.eventos.guardar', $artista) }}" enctype="multipart/form-data" x-ref="form"
                              @submit="enviando = true">
                            @csrf
                            <input type="hidden" name="item_id" :value="editando ? editando.id : ''">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Título del evento *</label>
                                    <input type="text" name="titulo" required
                                           value="{{ old('titulo') }}"
                                           placeholder="Ej: Concierto acústico en el Cerro Alegre"
                                           class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-violet-400">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Tipo de evento *</label>
                                    <select name="tipo" class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-violet-400">
                                        @foreach($tiposEvento as $slug => $info)
                                            <option value="{{ $slug }}" @selected(old('tipo') === $slug)>
                                                {{ $info['emoji'] }} {{ $info['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Fecha *</label>
                                    <input type="date" name="fecha" required
                                           value="{{ old('fecha') }}"
                                           class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-violet-400">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Hora de inicio</label>
                                    <input type="time" name="hora"
                                           value="{{ old('hora') }}"
                                           class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-violet-400">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Hora de término</label>
                                    <input type="time" name="hora_fin"
                                           value="{{ old('hora_fin') }}"
                                           class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-violet-400">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Precio (0 = gratuito)</label>
                                    <input type="number" name="precio" min="0" step="100"
                                           value="{{ old('precio') }}"
                                           placeholder="Ej: 5000"
                                           class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-violet-400">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Texto de precio (reemplaza al número)</label>
                                    <input type="text" name="precio_texto"
                                           value="{{ old('precio_texto') }}"
                                           placeholder="Ej: Desde $3.000 · Entrada liberada"
                                           class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-violet-400">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Descripción</label>
                                    <textarea name="descripcion" rows="6"
                                              placeholder="Detalles del evento, invitados, duración..."
                                              class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-violet-400 resize-none">{{ old('descripcion') }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Link de compra de entradas</label>
                                    <input type="url" name="url_entradas"
                                           value="{{ old('url_entradas') }}"
                                           placeholder="https://..."
                                           class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-violet-400">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Imagen del evento (opcional)</label>
                                    <input type="file" name="imagen" accept="image/*"
                                           @change="previsualizar($event.target)"
                                           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                                    <p class="text-[11px] text-gray-400 mt-1">JPG, PNG o WEBP — máx. 20 MB</p>
                                    <p x-show="imagenError" x-text="imagenError" class="text-xs text-red-500 font-semibold mt-1"></p>

                                    <div class="flex items-center gap-4 mt-2" x-show="imagenActual || imagenPreview">
                                        <div x-show="imagenActual" class="text-center">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Actual</p>
                                            <img :src="imagenActual" class="w-16 h-16 rounded-xl object-cover border border-gray-200">
                                        </div>
                                        <div x-show="imagenPreview" class="text-center">
                                            <p class="text-[10px] font-bold text-violet-500 uppercase mb-1">Nueva</p>
                                            <img :src="imagenPreview" class="w-16 h-16 rounded-xl object-cover border border-violet-200">
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-2 flex items-center gap-3">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="destacado" value="1"
                                               @checked(old('destacado'))
                                               class="rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                                        <span class="text-sm text-gray-700 font-medium">Destacar este evento</span>
                                    </label>
                                    <span class="text-xs text-gray-400">(aparece primero en tu perfil público)</span>
                                </div>
                            </div>

                            <div class="flex justify-end items-center mt-5 gap-3">
                                <button type="button" @click="abierto = false; resetForm()"
                                        :disabled="enviando"
                                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">Cancelar</button>
                                <button type="submit"
                                        :disabled="enviando"
                                        class="px-6 py-2 bg-violet-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    <svg x-show="enviando" x-cloak class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                    <span x-text="enviando ? 'Publicando…' : (editando ? 'Guardar cambios' : 'Publicar evento')"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Listado --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h4 class="font-bold text-gray-700 mb-4">Eventos programados</h4>

                    @php
                        $proximosEventos = $eventos->filter(fn($e) => $e->fecha && $e->fecha->greaterThanOrEqualTo(today()))->sortBy('fecha');
                        $pasadosEventos  = $eventos->filter(fn($e) => $e->fecha && $e->fecha->lessThan(today()))->sortByDesc('fecha');
                    @endphp

                    @if($eventos->count())
                        @if($proximosEventos->count())
                        <div class="space-y-3 mb-6">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Próximos</p>
                            @foreach($proximosEventos as $evento)
                            @php $tipoInfo = $evento->tipoEvento(); @endphp
                            <div class="flex items-start gap-4 border border-gray-100 rounded-xl p-4
                                {{ $evento->destacado ? 'border-violet-200 bg-violet-50/30' : '' }}">
                                @if($evento->imagen)
                                    <img src="{{ asset('storage/' . $evento->imagen) }}" alt="{{ $evento->titulo }}"
                                         class="w-16 h-16 rounded-xl object-cover border border-gray-100 shrink-0">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-gray-50 flex items-center justify-center text-2xl shrink-0 border border-gray-100">
                                        {{ $tipoInfo['emoji'] }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="font-semibold text-gray-800 text-sm">{{ $evento->titulo }}</p>
                                        @if($evento->destacado)
                                            <span class="text-[10px] font-black uppercase bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full">Destacado</span>
                                        @endif
                                        <span class="text-[10px] font-black uppercase bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                            {{ $tipoInfo['emoji'] }} {{ $tipoInfo['label'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        📅 {{ $evento->fecha->translatedFormat('l d \d\e F Y') }}
                                        @if($evento->hora)· 🕐 {{ \Carbon\Carbon::parse($evento->hora)->format('H:i') }}@endif
                                        @if($evento->hora_fin)– {{ \Carbon\Carbon::parse($evento->hora_fin)->format('H:i') }}@endif
                                    </p>
                                    <p class="text-xs font-bold text-violet-700 mt-0.5">{{ $evento->precioEvento() }}</p>
                                </div>
                                <div class="flex gap-2 shrink-0">
                                    <button type="button"
                                            @click="editar(@js([
                                                'id'           => $evento->id,
                                                'titulo'       => $evento->titulo,
                                                'tipo'         => $evento->tipo,
                                                'fecha'        => optional($evento->fecha)->format('Y-m-d'),
                                                'hora'         => $evento->hora,
                                                'hora_fin'     => $evento->hora_fin,
                                                'precio'       => $evento->precio,
                                                'precio_texto' => $evento->precio_texto,
                                                'descripcion'  => $evento->descripcion,
                                                'url_entradas' => $evento->url_entradas,
                                                'destacado'    => (bool) $evento->destacado,
                                                'imagen_url'   => $evento->imagen ? asset('storage/' . $evento->imagen) : null,
                                            ])); window.scrollTo({ top: 0, behavior: 'smooth' })"
                                            class="text-xs text-violet-600 font-bold hover:underline">Editar</button>
                                    <form method="POST" action="{{ route('artista.eventos.eliminar', [$artista, $evento]) }}"
                                          onsubmit="return confirm('¿Eliminar este evento?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 font-bold hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if($pasadosEventos->count())
                        <details class="mt-4">
                            <summary class="text-xs font-black uppercase tracking-widest text-gray-400 cursor-pointer hover:text-gray-600">
                                Eventos pasados ({{ $pasadosEventos->count() }})
                            </summary>
                            <div class="space-y-2 mt-3">
                                @foreach($pasadosEventos as $evento)
                                <div class="flex items-center justify-between border border-gray-100 rounded-xl p-3 opacity-60">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">{{ $evento->titulo }}</p>
                                        <p class="text-xs text-gray-400">{{ $evento->fecha->translatedFormat('d M Y') }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('artista.eventos.eliminar', [$artista, $evento]) }}"
                                          onsubmit="return confirm('¿Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">Eliminar</button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                        </details>
                        @endif
                    @else
                        <p class="text-sm text-gray-400 italic text-center py-6">No tienes eventos programados. ¡Publica el primero!</p>
                    @endif
                </div>

            </div>

        </div>
    </div>

<script>
function eliminarImagenArtista(id, url, token) {
    if (!confirm('¿Eliminar esta imagen?')) return;
    fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
    }).then(res => {
        if (!res.ok) throw new Error();
        document.getElementById('img-wrap-' + id)?.remove();
    }).catch(() => alert('No se pudo eliminar la imagen.'));
}
</script>
</x-app-layout>
