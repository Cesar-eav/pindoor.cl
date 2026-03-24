<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nuevo Punto de Interés General') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <form action="{{ route('admin.puntos.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="title" :value="__('Nombre del Punto (Mirador, Mural, etc)')" />
                            <x-text-input id="title" name="title" class="block mt-1 w-full" required />
                        </div>
                        <div>
                            <x-input-label for="category" :value="__('Categoría General')" />
                            <select name="category" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-pindoor-accent">
                                <option value="Mirador">Mirador 🚠</option>
                                <option value="Mural">Mural / Street Art 🎨</option>
                                <option value="Escalera">Escalera Icónica 🪜</option>
                                <option value="Plaza">Plaza / Parque 🌳</option>
                                <option value="Monumento">Monumento Histórico 🏛️</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="sector" :value="__('Sector / Cerro')" />
                        <select name="sector" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-pindoor-accent">
                            <option value="Cerro Alegre">Cerro Alegre</option>
                            <option value="Cerro Concepción">Cerro Concepción</option>
                            <option value="Playa Ancha">Playa Ancha</option>
                            <option value="Cerro Artillería">Cerro Artillería</option>
                            <option value="Cerro Cordillera">Cerro Cordillera</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="description" :value="__('Breve reseña histórica o descripción')" />
                        <textarea name="description" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-pindoor-accent"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <x-input-label for="lat" :value="__('Latitud (Para el mapa)')" />
                            <x-text-input name="lat" class="block mt-1 w-full" placeholder="-33.047..." />
                        </div>
                        <div>
                            <x-input-label for="lng" :value="__('Longitud (Para el mapa)')" />
                            <x-text-input name="lng" class="block mt-1 w-full" placeholder="-71.629..." />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button class="bg-gray-800">
                            {{ __('Guardar Punto Público') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>