<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar nuevo punto en Valparaíso') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                
                <form action="{{ route('cliente.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="title" :value="__('Nombre del Local')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" required autofocus placeholder="Ej: Café del Cerro" />
                        </div>
                        <div>
                            <x-input-label for="category" :value="__('Categoría')" />
                            <select name="category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Cafetería">Cafetería</option>
                                <option value="Restaurante">Restaurante</option>
                                <option value="Hostal">Hostal/Hotel</option>
                                <option value="Tienda">Tienda de Diseño</option>
                                <option value="Museo">Cultura/Museo</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="sector" :value="__('Sector o Cerro')" />
                        <select name="sector" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <optgroup label="Cerros Turísticos">
                                <option value="Cerro Alegre">Cerro Alegre</option>
                                <option value="Cerro Concepción">Cerro Concepción</option>
                                <option value="Cerro Artillería">Cerro Artillería</option>
                                <option value="Cerro Bellavista">Cerro Bellavista</option>
                                <option value="Cerro Florida">Cerro Florida</option>
                            </optgroup>
                            <optgroup label="Plan y Otros">
                                <option value="El Plan">El Plan (Centro)</option>
                                <option value="Barrio Puerto">Barrio Puerto</option>
                                <option value="Playa Ancha">Playa Ancha</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="description" :value="__('Descripción del lugar')" />
                        <textarea name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Cuéntale a los turistas qué hace especial a tu local..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="lat" :value="__('Latitud')" />
                            <x-text-input id="lat" class="block mt-1 w-full" type="text" name="lat" placeholder="-33.0472" />
                        </div>
                        <div>
                            <x-input-label for="lng" :value="__('Longitud')" />
                            <x-text-input id="lng" class="block mt-1 w-full" type="text" name="lng" placeholder="-71.6297" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <x-input-label for="video_url" :value="__('Link de YouTube (Opcional)')" />
                            <x-text-input id="video_url" class="block mt-1 w-full" type="text" name="video_url" placeholder="https://youtube.com/..." />
                        </div>
                        <div>
                            <x-input-label for="horario" :value="__('Horario')" />
                            <x-text-input id="horario" class="block mt-1 w-full" type="text" name="horario" placeholder="Lun-Vie 09:00 a 20:00" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <x-primary-button class="bg-pindoor-accent ml-3">
                            {{ __('Guardar y Continuar a Fotos') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>