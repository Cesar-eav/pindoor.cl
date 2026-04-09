<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('cliente.perfil') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Volver</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar mi perfil</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cliente.perfil.actualizar') }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Info básica --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-bold mb-4">Información del negocio</p>

                        {{-- Solo lectura: título y ubicación (los controla el admin) --}}
                        <div class="bg-gray-50 border border-dashed border-gray-200 rounded-xl p-4 mb-5">
                            <p class="text-xs text-gray-400 mb-1">Gestionado por el administrador</p>
                            <div class="font-semibold text-gray-700">{{ $punto->title }}</div>
                            <div class="text-sm text-gray-400">
                                {{ $punto->categoria?->icono }} {{ $punto->categoria?->nombre ?? '—' }}
                                &middot; 📍 {{ $punto->sector }}
                                &middot; {{ $punto->direccion }}
                            </div>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <x-input-label for="description" value="Descripción *" />
                        <textarea id="description" name="description" rows="5" required
                                  class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm text-sm focus:ring-pindoor-accent resize-none"
                                  placeholder="Cuéntale al turista qué es tu negocio, qué ofreces, qué lo hace especial..."
                        >{{ old('description', $punto->description) }}</textarea>
                    </div>

                    {{-- Horario --}}
                    <div>
                        <x-input-label for="horario" value="Horario de atención" />
                        <x-text-input id="horario" name="horario" class="block mt-1 w-full"
                                      value="{{ old('horario', $punto->horario) }}"
                                      placeholder="Ej: Lun–Vie 09:00–20:00, Sáb 10:00–14:00" />
                    </div>

                    {{-- Enlace --}}
                    <div>
                        <x-input-label for="enlace" value="Web o Instagram" />
                        <x-text-input id="enlace" name="enlace" type="url" class="block mt-1 w-full"
                                      value="{{ old('enlace', $punto->enlace) }}"
                                      placeholder="https://instagram.com/minegocio" />
                    </div>

                    {{-- Tags --}}
                    <div>
                        <x-input-label for="tags" value="Etiquetas (separadas por coma)" />
                        <x-text-input id="tags" name="tags" class="block mt-1 w-full"
                                      value="{{ old('tags', is_array($punto->tags) ? implode(', ', $punto->tags) : '') }}"
                                      placeholder="café, vegano, terraza, wifi" />
                        <p class="text-xs text-gray-400 mt-1">Estas etiquetas aparecen en tu ficha pública.</p>
                    </div>
                </div>

                {{-- Imagen de perfil --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-4">Logo / Imagen de perfil</p>

                    <div class="flex items-center gap-5">
                        @if($punto->imagen_perfil)
                            <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                 alt="Logo actual"
                                 class="w-20 h-20 rounded-2xl object-cover border border-gray-100 shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center shrink-0 text-3xl">
                                🏪
                            </div>
                        @endif

                        <div class="flex-1">
                            <input type="file" name="imagen_perfil" id="imagen_perfil"
                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                          file:text-sm file:font-bold file:bg-gray-100 file:text-gray-700
                                          hover:file:bg-gray-200 cursor-pointer" />
                            <p class="text-xs text-gray-400 mt-2">JPG, PNG o WEBP. Máximo 2 MB.</p>
                        </div>
                    </div>
                </div>

                {{-- Perfil de búsqueda --}}
                <div id="busqueda" class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                    <p class="text-xs text-amber-700 uppercase font-bold mb-1">Perfil de búsqueda</p>
                    <p class="text-xs text-amber-600 mb-4">
                        Este texto <strong>no se muestra a los turistas</strong>, pero es clave para que te encuentren.
                        Describe todo lo que ofreces: tipos de café, comidas, opciones veganas/celíacas, ambiente, etc.
                    </p>
                    <textarea id="descripcion_busqueda" name="descripcion_busqueda" rows="6"
                              class="block w-full border-amber-200 bg-white rounded-xl shadow-sm text-sm focus:ring-amber-400 resize-none"
                              placeholder="Cafetería especialidad, V60, chemex, aeropress, capuchino, latte, cortado, leche de avena, leche de almendras, desayunos, tostadas, croissant, vegano, sin gluten, terraza exterior, perros permitidos, wifi gratis, enchufes, buena luz, trabajo remoto..."
                    >{{ old('descripcion_busqueda', $punto->descripcion_busqueda) }}</textarea>
                </div>

                {{-- Botones --}}
                <div class="flex justify-between items-center pb-4">
                    <a href="{{ route('cliente.perfil') }}"
                       class="text-sm text-gray-500 hover:text-gray-700">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
