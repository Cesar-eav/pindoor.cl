<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Artículo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('articulos.update', $articulo) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="revista_id" :value="__('Revista')" />
                            <select id="revista_id" class="block mt-1 w-full text-black" name="revista_id" required>
                                <option value="">{{ __('Seleccionar Revista') }}</option>
                                @foreach ($revistas as $revista)
                                    <option value="{{ $revista->id }}" {{ old('revista_id', $articulo->revista_id) == $revista->id ? 'selected' : '' }}>
                                        {{ $revista->titulo }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('revista_id')" class="mt-2" />
                        </div>

                                                <div>
                            <x-input-label for="columnista_id" :value="__('Columnista')" />
                            <select id="columnista_id" class="block mt-1 w-full text-black" name="columnista_id" required>
                                <option value="">{{ __('Seleccionar columnista') }}</option>
                                @foreach ($columnistas as $columnista)
                                    <option value="{{ $columnista->id }}" {{ old('columnista_id', $articulo->columnista_id) == $columnista->id ? 'selected' : '' }}>
                                        {{ $columnista->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('revista_id')" class="mt-2" />
                        </div>
        
                        <div class="mt-4">
                            <x-input-label for="titulo" :value="__('Título')" />
                            <x-text-input id="titulo" class="block mt-1 w-full  text-black" type="text" name="titulo" :value="old('titulo', $articulo->titulo)" required autofocus />
                            <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="contenido" :value="__('Contenido')" />
                            <x-forms.textarea id="descripcion" class="block mt-1 w-full text-black" name="descripcion">
                                {{ old('contenido', $articulo->contenido) }}
                            </x-forms.textarea>
                        <x-input-error :messages="$errors->get('contenido')" class="mt-2" />
                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Actualizar Artículo') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>