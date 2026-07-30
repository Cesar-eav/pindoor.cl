<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crea tu perfil de operador turístico</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

                <p class="text-sm text-gray-500 mb-6">Cuéntanos quién eres o de qué empresa de turismo formas parte. Podrás elegir los lugares que operas y completar el resto del perfil después.</p>

                @if($errors->any())
                    <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('operador.crear') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Nombre del operador o empresa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ciudad</label>
                        <input type="text" name="ciudad" value="{{ old('ciudad') }}"
                               placeholder="Valparaíso, Santiago…"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Foto de perfil / logo</label>
                        <input type="file" name="imagen" accept="image/*"
                               class="block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                                      file:text-sm file:font-bold file:bg-teal-50 file:text-teal-700
                                      hover:file:bg-teal-100 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — máx. 4 MB</p>
                    </div>

                    <button type="submit"
                            class="w-full bg-teal-600 text-white py-2.5 rounded-xl font-bold text-sm hover:bg-teal-700 transition">
                        Crear mi perfil
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
