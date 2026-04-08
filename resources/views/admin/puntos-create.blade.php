<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data="{}">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Puntos Públicos') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ view: 'listado' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex mb-6 bg-gray-200 p-1 rounded-xl w-fit">
                <button 
                    @click="view = 'crear'" 
                    :class="view === 'crear' ? 'bg-white shadow-sm text-pindoor-accent' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-2 rounded-lg font-bold text-sm transition-all"
                >
                    + Crear Nuevo
                </button>
                <button 
                    @click="view = 'listado'" 
                    :class="view === 'listado' ? 'bg-white shadow-sm text-pindoor-accent' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-2 rounded-lg font-bold text-sm transition-all"
                >
                    Ver Listado
                </button>
            </div>

            <div x-show="view === 'crear'" x-cloak x-transition>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 max-w-4xl mx-auto">
                    <h3 class="text-lg font-bold mb-6">Información del Nuevo Punto Público</h3>
                    <form action="{{ route('admin.puntos.store') }}" id="main-form" onsubmit="return false;">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="title" :value="__('Nombre del Punto')" />
                                <x-text-input id="title" name="title" class="block mt-1 w-full" required />
                            </div>
                            <div>
                                <x-input-label for="category" :value="__('Categoría')" />
                                <select name="categoria_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-pindoor-accent">
                                    <option value="">Selecciona una categoría</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nombre }} {{ $cat->icono ? '—' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="autor" :value="__('Autor')" />
                                <x-text-input id="autor" name="autor" class="block mt-1 w-full" placeholder="Artista o Institución" />
                            </div>
                            <div>
                                <x-input-label for="tags" :value="__('Etiquetas')" />
                                <x-text-input id="tags" name="tags" class="block mt-1 w-full" placeholder="vista, colores, historia" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="sector" :value="__('Sector / Cerro')" />
                            <select name="sector" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="Cerro Alegre">Plan</option>
                                <option value="Cerro Alegre">Cerro Alegre</option>
                                <option value="Cerro Concepción">Cerro Concepción</option>
                                <option value="Cerro Alegre">Cerro San Juan de Dios</option>
                                <option value="Cerro Alegre">Cerro Bellavista</option>
                                <option value="Cerro Alegre">Cerro Monjas</option>
                                <option value="Cerro Alegre">Cerro Mariposas</option>
                                <option value="Cerro Alegre">Cerro Florida</option>
                                <option value="Playa Ancha">Playa Ancha</option>
                                <option value="Cerro Artillería">Cerro Artillería</option>
                                <option value="Cerro Alegre">Cerro Esperanza</option>
                                <option value="Cerro Alegre">Cerro Placeres</option>
                                <option value="Cerro Alegre">Cerro Barón</option>
                                <option value="Cerro Alegre">Cerro Lecheros</option>
                                <option value="Cerro Alegre">Cerro Larraín</option>
                                <option value="Cerro Alegre">Cerro Polanco</option>
                                <option value="Cerro Alegre">Cerro Molino</option>
                                <option value="Cerro Alegre">Cerro Rodríguez</option>
                                <option value="Cerro Alegre">Cerro Rocuant</option>
                                <option value="Cerro Alegre">Cerro Delicias</option>
                                <option value="Cerro Alegre">Cerro O'Higgins</option>
                                <option value="Cerro Alegre">Cerro San Roque</option>
                                 <option value="Cerro Alegre">Cerro Rodríguez</option>
                                <option value="Cerro Alegre">Cerro Ramaditas</option>                               
                                 <option value="Cerro Alegre">Cerro Merced</option>
                                <option value="Cerro Alegre">Cerro Las Cañas</option>                           
                                <option value="Cerro Alegre">Cerro El Litre</option>
                                <option value="Cerro Alegre">Cerro La Cruz</option>


                                <option value="Cerro Alegre">Cerro Jiménez</option>
                                <option value="Cerro Alegre">Cerro La Loma</option>
                                <option value="Cerro Alegre">Cerro Yungay</option>
                                <option value="Cerro Alegre">Cerro Panteón</option>
                                <option value="Cerro Alegre">Cerro Cárcel</option>


                                <option value="Cerro Cordillera">Cerro Mesilla</option>
                                <option value="Cerro Cordillera">Cerro Toro</option>
                                <option value="Cerro Cordillera">Cerro Santo Domingo</option>
                                <option value="Cerro Cordillera">Cerro Arrayán</option>
                                <option value="Cerro Cordillera">Cerro Perdices</option>
                                <option value="Cerro Cordillera">Placilla</option>
                                <option value="Cerro Cordillera">Laguna Verde</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Reseña o descripción')" />
                            <textarea name="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>

                        <div id="app" class="mb-6"> 
                            <selector-mapa></selector-mapa>
                            <galeria-subida></galeria-subida> 
                        </div>

                        <div class="flex justify-end">
                            <button 
                                type="button"
                                onclick="window.dispatchEvent(new CustomEvent('trigger-pindoor-submit'))"
                                class="bg-pindoor-accent text-white px-8 py-3 rounded-2xl font-bold shadow-lg hover:bg-red-600 transition"
                            >
                                {{ __('Publicar Punto Público') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="view === 'listado'" x-cloak x-transition>
                <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                            <tr>
                                <th class="px-6 py-4">Punto</th>
                                <th class="px-6 py-4">Sector</th>
                                <th class="px-6 py-4">Categoría</th>
                                <th class="px-6 py-4 text-center">Estado</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($puntos as $punto)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $punto->title }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $punto->sector }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-gray-100 px-2 py-1 rounded text-[10px]">{{ $punto->category }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $punto->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $punto->activo ? 'ACTIVO' : 'OCULTO' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="#" class="text-blue-600 font-bold hover:underline">Editar</a>

                                    <form action="{{ route('admin.puntos.toggle', $punto) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="font-bold {{ $punto->activo ? 'text-orange-600' : 'text-green-600' }} hover:underline">
                                            {{ $punto->activo ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($puntos->isEmpty())
                        <div class="p-10 text-center text-gray-400 italic">No has creado puntos públicos todavía.</div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<style>
    [x-cloak] { display: none !important; }
</style>
