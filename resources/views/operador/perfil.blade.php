<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi perfil de operador turístico</h2>
            <a href="{{ route('operador.show', $operador->slug) }}" target="_blank"
               class="text-xs font-bold text-teal-600 hover:underline flex items-center gap-1">
                Ver perfil público
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ tab: 'perfil' }">
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
                            ? 'bg-teal-600 text-white shadow-sm'
                            : 'text-gray-500 hover:text-gray-800'"
                        class="flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Perfil
                </button>
                <button type="button" @click="tab = 'lugares'"
                        :class="tab === 'lugares'
                            ? 'bg-teal-600 text-white shadow-sm'
                            : 'text-gray-500 hover:text-gray-800'"
                        class="flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Lugares
                    @if($operador->puntos->isNotEmpty())
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                              :class="tab === 'lugares' ? 'bg-white/20 text-white' : 'bg-teal-100 text-teal-600'">
                            {{ $operador->puntos->count() }}
                        </span>
                    @endif
                </button>
            </div>

            {{-- Pestaña: Perfil --}}
            <div x-show="tab === 'perfil'" x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">

                <form action="{{ route('operador.perfil.actualizar') }}" method="POST"
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
                                    @if($operador->imagen_perfil)
                                        <img src="{{ asset('storage/' . $operador->imagen_perfil) }}"
                                             alt="{{ $operador->nombre }}"
                                             class="w-20 h-20 lg:w-36 lg:h-36 rounded-full object-cover border-4 border-teal-100 shadow-sm shrink-0">
                                    @else
                                        <div class="w-20 h-20 lg:w-36 lg:h-36 rounded-full bg-teal-100 flex items-center justify-center text-4xl lg:text-5xl border-4 border-teal-50 shrink-0">🧭</div>
                                    @endif
                                    <div class="flex-1 lg:w-full">
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">Foto de perfil / logo</label>
                                        <input type="file" name="imagen" accept="image/*"
                                               class="block w-full text-xs text-gray-500
                                                      file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0
                                                      file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700
                                                      hover:file:bg-teal-100 cursor-pointer">
                                        <p class="text-[10px] text-gray-400 mt-1">JPG, PNG — máx. 4 MB</p>
                                    </div>
                                </div>

                                {{-- Campos en grid 2 cols --}}
                                <div class="flex-1 grid sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre del operador o empresa *</label>
                                        <input type="text" name="nombre" value="{{ old('nombre', $operador->nombre) }}" required
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ciudad</label>
                                        <input type="text" name="ciudad" value="{{ old('ciudad', $operador->ciudad) }}"
                                               placeholder="Valparaíso…"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono</label>
                                        <input type="text" name="telefono" value="{{ old('telefono', $operador->telefono) }}"
                                               placeholder="+56 9 1234 5678"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
                                        <textarea name="descripcion" rows="7"
                                                  placeholder="Cuéntanos sobre tus tours, recorridos y experiencia…"
                                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none resize-none">{{ old('descripcion', $operador->descripcion) }}</textarea>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email de contacto público</label>
                                        <input type="email" name="email_contacto" value="{{ old('email_contacto', $operador->email_contacto) }}"
                                               placeholder="contacto@tuempresa.cl"
                                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Redes: col derecha con guardar al pie --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3 h-full">
                            <h3 class="font-bold text-gray-800">Redes y contacto</h3>

                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                                    <span class="w-5 h-5 rounded flex items-center justify-center shrink-0" style="background:#25D366">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    </span>
                                    WhatsApp
                                </label>
                                <input type="url" name="enlace_whatsapp" value="{{ old('enlace_whatsapp', $operador->enlace_whatsapp) }}"
                                       placeholder="https://wa.me/56912345678"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>

                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                                    <span class="w-5 h-5 rounded flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#f9a825,#e91e63,#9c27b0)">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </span>
                                    Instagram
                                </label>
                                <input type="url" name="enlace_instagram" value="{{ old('enlace_instagram', $operador->enlace_instagram) }}"
                                       placeholder="https://instagram.com/tu_usuario"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>

                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                                    <span class="w-5 h-5 rounded flex items-center justify-center shrink-0" style="background:#1877F2">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </span>
                                    Facebook
                                </label>
                                <input type="url" name="enlace_facebook" value="{{ old('enlace_facebook', $operador->enlace_facebook) }}"
                                       placeholder="https://facebook.com/tu_pagina"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>

                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                                    <span class="w-5 h-5 rounded bg-gray-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    </span>
                                    Sitio web
                                </label>
                                <input type="url" name="enlace_web" value="{{ old('enlace_web', $operador->enlace_web) }}"
                                       placeholder="https://tu-sitio.cl"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>

                            <div class="pt-2 border-t border-gray-100 mt-auto">
                                <button type="submit"
                                        :disabled="guardando"
                                        class="w-full bg-teal-600 hover:bg-teal-700 disabled:opacity-70 text-white px-6 py-3 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
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

            {{-- Pestaña: Lugares --}}
            <div x-show="tab === 'lugares'" x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                 style="display:none">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">
                        Lugares que visitas en tus tours
                    </label>
                    <p class="text-xs text-gray-400 mb-3">
                        Elige los miradores, museos y otros puntos de interés a los que llevas turistas. Aparecerás en la ficha
                        pública de cada uno, en la sección "¿Quién te puede llevar aquí?".
                    </p>

                    <form action="{{ route('operador.perfil.lugares') }}" method="POST"
                          x-data="{
                              todos: {{ Illuminate\Support\Js::from($puntosData) }},
                              categorias: {{ Illuminate\Support\Js::from($categoriasDisponibles) }},
                              seleccionados: {{ Illuminate\Support\Js::from($puntosSeleccionados) }},
                              busqueda: '',
                              categoria: '',
                              get filtrados() {
                                  const q = this.busqueda.trim().toLowerCase();
                                  return this.todos.filter(p =>
                                      !this.seleccionados.includes(p.id) &&
                                      (this.categoria === '' || p.categoria === this.categoria) &&
                                      (q === '' || (p.title + ' ' + (p.sector || '')).toLowerCase().includes(q))
                                  );
                              },
                              agregar(id) { if (!this.seleccionados.includes(id)) this.seleccionados.push(id); },
                              quitar(id) { this.seleccionados = this.seleccionados.filter(x => x !== id); },
                              info(id) { return this.todos.find(p => p.id === id) || {}; },
                          }">
                        @csrf @method('PUT')

                        {{-- Seleccionados --}}
                        <div class="flex flex-wrap gap-2 mb-3" x-show="seleccionados.length">
                            <template x-for="id in seleccionados" :key="id">
                                <span class="inline-flex items-center gap-1.5 bg-teal-50 text-teal-700 text-xs font-bold pl-3 pr-1.5 py-1.5 rounded-full">
                                    <span x-text="info(id).title"></span>
                                    <button type="button" @click="quitar(id)"
                                            class="hover:bg-teal-700/20 rounded-full w-4 h-4 flex items-center justify-center leading-none">×</button>
                                </span>
                            </template>
                        </div>
                        <p x-show="!seleccionados.length" class="text-xs text-gray-300 italic mb-3">Ningún lugar seleccionado todavía.</p>

                        @if($puntosData->isEmpty())
                            <div class="text-center py-10 text-gray-400 border border-dashed border-gray-200 rounded-xl">
                                <p class="text-sm font-medium">Todavía no hay categorías habilitadas para operadores turísticos.</p>
                                <p class="text-xs mt-1">Escríbenos y le pedimos al equipo de Pindoor que habilite las categorías que necesitas.</p>
                            </div>
                        @else
                            {{-- Filtro por categoría --}}
                            <div class="flex flex-wrap gap-1.5 mb-2">
                                <button type="button" @click="categoria = ''"
                                        :class="categoria === '' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                                        class="px-3 py-1 rounded-full text-xs font-bold transition">Todas</button>
                                <template x-for="cat in categorias" :key="cat">
                                    <button type="button" @click="categoria = cat"
                                            :class="categoria === cat ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                                            class="px-3 py-1 rounded-full text-xs font-bold transition" x-text="cat"></button>
                                </template>
                            </div>

                            {{-- Buscador --}}
                            <input type="text" x-model="busqueda" placeholder="Busca por nombre o sector…"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none mb-2">

                            {{-- Resultados --}}
                            <div class="border border-gray-100 rounded-xl divide-y divide-gray-50 max-h-72 overflow-y-auto">
                                <template x-for="p in filtrados.slice(0, 50)" :key="p.id">
                                    <button type="button" @click="agregar(p.id)"
                                            class="w-full text-left px-4 py-2.5 hover:bg-gray-50 flex items-center justify-between gap-2 text-sm transition">
                                        <span>
                                            <span class="font-medium text-gray-800" x-text="p.title"></span>
                                            <span class="text-gray-400" x-text="p.sector ? ' · ' + p.sector : ''"></span>
                                        </span>
                                        <span class="text-[10px] text-gray-400 shrink-0" x-text="p.categoria ? (p.emoji ?? '') + ' ' + p.categoria : ''"></span>
                                    </button>
                                </template>
                                <p x-show="!filtrados.length" class="px-4 py-3 text-xs text-gray-300 italic">Sin resultados.</p>
                            </div>
                            <p x-show="filtrados.length > 50" class="text-[11px] text-gray-400 mt-1">Mostrando los primeros 50 — refina la búsqueda para ver más.</p>
                        @endif

                        <template x-for="id in seleccionados" :key="'input-' + id">
                            <input type="hidden" name="puntos[]" :value="id">
                        </template>

                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <button type="submit"
                                    class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition">
                                Guardar lugares
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
