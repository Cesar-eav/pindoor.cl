<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi perfil de artista</h2>
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
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{-- Datos principales --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="font-bold text-gray-800 mb-5">Información del perfil</h3>

                <form action="{{ route('artista.perfil.actualizar') }}" method="POST"
                      enctype="multipart/form-data" class="space-y-5">
                    @csrf @method('PUT')

                    {{-- Foto --}}
                    <div class="flex items-center gap-5 mb-2">
                        @if($artista->imagen_perfil)
                            <img src="{{ asset('storage/' . $artista->imagen_perfil) }}"
                                 alt="{{ $artista->nombre }}"
                                 class="w-20 h-20 rounded-full object-cover border-2 border-violet-200">
                        @else
                            <div class="w-20 h-20 rounded-full bg-violet-100 flex items-center justify-center text-3xl">🎨</div>
                        @endif
                        <div class="flex-1">
                            <input type="file" name="imagen" accept="image/*"
                                   class="block w-full text-sm text-gray-500
                                          file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                          file:text-xs file:font-bold file:bg-violet-50 file:text-violet-700
                                          hover:file:bg-violet-100 cursor-pointer">
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — máx. 4 MB</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre artístico *</label>
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
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción / Biografía</label>
                        <textarea name="descripcion" rows="4"
                                  placeholder="Cuéntanos sobre tu trabajo, trayectoria y propuesta artística…"
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none resize-none">{{ old('descripcion', $artista->descripcion) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
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
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email de contacto público</label>
                        <input type="email" name="email_contacto" value="{{ old('email_contacto', $artista->email_contacto) }}"
                               placeholder="contacto@tumisma.cl"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                        <p class="text-xs text-gray-400 mt-1">Este email se mostrará en tu perfil público (puede ser distinto al de tu cuenta)</p>
                    </div>

                    {{-- Redes --}}
                    <div class="border-t border-gray-100 pt-5">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Redes y enlaces</p>
                        <div class="space-y-3">
                            @foreach([
                                ['enlace_web',       '🌐', 'Sitio web',  'https://tu-sitio.cl'],
                                ['enlace_instagram',  '📸', 'Instagram',  'https://instagram.com/tu_usuario'],
                                ['enlace_facebook',   '👤', 'Facebook',   'https://facebook.com/tu_pagina'],
                                ['enlace_spotify',    '🎧', 'Spotify',    'https://open.spotify.com/artist/...'],
                                ['enlace_youtube',    '▶️',  'YouTube',    'https://youtube.com/@tu_canal'],
                            ] as [$campo, $icon, $label, $placeholder])
                            <div class="flex items-center gap-3">
                                <span class="text-lg w-6 text-center shrink-0">{{ $icon }}</span>
                                <input type="url" name="{{ $campo }}"
                                       value="{{ old($campo, $artista->$campo) }}"
                                       placeholder="{{ $placeholder }}"
                                       class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit"
                            class="bg-violet-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-violet-700 transition">
                        Guardar cambios
                    </button>
                </form>
            </div>

            {{-- Portafolio / Galería --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="font-bold text-gray-800 mb-4">Portafolio</h3>

                @if($artista->imagenes->isNotEmpty())
                    <div class="flex flex-wrap gap-3 mb-5">
                        @foreach($artista->imagenes as $img)
                            <div class="relative group" id="img-wrap-{{ $img->id }}">
                                <img src="{{ asset('storage/' . $img->ruta) }}"
                                     alt="Portafolio"
                                     class="w-28 h-28 object-cover rounded-xl border border-gray-200">
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
                      enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="file" name="imagenes[]" accept="image/*" multiple
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                                  file:text-sm file:font-bold file:bg-violet-50 file:text-violet-700
                                  hover:file:bg-violet-100 cursor-pointer">
                    <p class="text-xs text-gray-400">Puedes subir varias imágenes — JPG, PNG, WEBP — máx. 4 MB c/u</p>
                    <button type="submit"
                            class="bg-gray-800 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-gray-700 transition">
                        Subir imágenes
                    </button>
                </form>
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
