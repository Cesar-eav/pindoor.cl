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

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

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

            {{-- Grid principal: form ocupa 2 cols, portafolio 1 col --}}
            <div class="grid lg:grid-cols-3 gap-6 items-start">

                {{-- Columnas 1 y 2: un solo form --}}
                <form action="{{ route('artista.perfil.actualizar') }}" method="POST"
                      enctype="multipart/form-data"
                      class="lg:col-span-2 grid lg:grid-cols-2 gap-6 items-start">
                    @csrf @method('PUT')

                    {{-- Col 1 — Información del perfil --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                        <h3 class="font-bold text-gray-800">Información del perfil</h3>

                        {{-- Foto --}}
                        <div class="flex items-center gap-4">
                            @if($artista->imagen_perfil)
                                <img src="{{ asset('storage/' . $artista->imagen_perfil) }}"
                                     alt="{{ $artista->nombre }}"
                                     class="w-16 h-16 rounded-full object-cover border-2 border-violet-200 shrink-0">
                            @else
                                <div class="w-16 h-16 rounded-full bg-violet-100 flex items-center justify-center text-2xl shrink-0">🎨</div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <input type="file" name="imagen" accept="image/*"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                              file:text-xs file:font-bold file:bg-violet-50 file:text-violet-700
                                              hover:file:bg-violet-100 cursor-pointer">
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — máx. 4 MB</p>
                            </div>
                        </div>

                        <div>
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
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción / Biografía</label>
                            <textarea name="descripcion" rows="4"
                                      placeholder="Cuéntanos sobre tu trabajo, trayectoria y propuesta artística…"
                                      class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none resize-none">{{ old('descripcion', $artista->descripcion) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Ciudad</label>
                            <input type="text" name="ciudad" value="{{ old('ciudad', $artista->ciudad) }}"
                                   placeholder="Valparaíso…"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono de contacto</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $artista->telefono) }}"
                                   placeholder="+56 9 1234 5678"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email de contacto público</label>
                            <input type="email" name="email_contacto" value="{{ old('email_contacto', $artista->email_contacto) }}"
                                   placeholder="contacto@tumisma.cl"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                            <p class="text-xs text-gray-400 mt-1">Se mostrará en tu perfil público</p>
                        </div>
                    </div>

                    {{-- Col 2 — Redes y enlaces --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                        <h3 class="font-bold text-gray-800">Redes y enlaces</h3>

                        <div class="space-y-3">
                            @foreach([
                                ['enlace_web',       '🌐', 'Sitio web',  'https://tu-sitio.cl'],
                                ['enlace_instagram',  '📸', 'Instagram',  'https://instagram.com/tu_usuario'],
                                ['enlace_facebook',   '👤', 'Facebook',   'https://facebook.com/tu_pagina'],
                                ['enlace_spotify',    '🎧', 'Spotify',    'https://open.spotify.com/artist/...'],
                                ['enlace_youtube',    '▶️',  'YouTube',    'https://youtube.com/@tu_canal'],
                            ] as [$campo, $icon, $label, $placeholder])
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">{{ $icon }} {{ $label }}</label>
                                <input type="url" name="{{ $campo }}"
                                       value="{{ old($campo, $artista->$campo) }}"
                                       placeholder="{{ $placeholder }}"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                            </div>
                            @endforeach
                        </div>

                        <button type="submit"
                                class="w-full bg-violet-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-violet-700 transition">
                            Guardar cambios
                        </button>
                    </div>

                </form>

                {{-- Col 3 — Portafolio --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h3 class="font-bold text-gray-800">Portafolio</h3>

                    @if($artista->imagenes->isNotEmpty())
                        <div class="grid grid-cols-3 gap-2">
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
                    @endif

                    <form action="{{ route('artista.imagen.subir') }}" method="POST"
                          enctype="multipart/form-data" class="space-y-3"
                          onsubmit="if(!this.querySelector('input[type=file]').files.length){ alert('Selecciona al menos una imagen antes de subir.'); return false; }">
                        @csrf
                        <input type="file" name="imagenes[]" accept="image/*" multiple
                               class="block w-full text-sm text-gray-500
                                      file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                      file:text-xs file:font-bold file:bg-violet-50 file:text-violet-700
                                      hover:file:bg-violet-100 cursor-pointer">
                        <p class="text-xs text-gray-400">JPG, PNG, WEBP — máx. 4 MB c/u</p>
                        <button type="submit"
                                class="w-full bg-gray-800 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-gray-700 transition">
                            Subir imágenes
                        </button>
                    </form>
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
