<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Revista') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" >
                    <form method="POST" action="{{ route('revistas.update', $revista) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="titulo" :value="__('Título')" />
                            <x-text-input id="titulo" class="block mt-1 w-full" type="text" name="titulo" :value="old('titulo', $revista->titulo)" required autofocus />
                            <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="fecha_publicacion" :value="__('Fecha de Publicación (Opcional)')" />
                            <x-text-input id="fecha_publicacion" class="block mt-1 w-full" type="date" name="fecha_publicacion" :value="old('fecha_publicacion', $revista->fecha_publicacion ? $revista->fecha_publicacion->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('fecha_publicacion')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="descripcion" :value="__('Descripción (Opcional)')" />
                            <x-forms.textarea id="descripcion" class="block mt-1 w-full" name="descripcion">
                                {{ old('descripcion') }}
                            </x-forms.textarea>                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Actualizar Revista') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>