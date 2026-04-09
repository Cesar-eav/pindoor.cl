<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.clientes') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Clientes</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Módulos — {{ $punto->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Paneles visibles para el cliente</p>
                <p class="text-xs text-gray-500 mb-6">
                    Solo los módulos activos aparecerán en el panel del cliente y en su ficha pública.
                    Los valores por defecto dependen de la categoría del negocio.
                </p>

                <form method="POST" action="{{ route('admin.clientes.modulos', $punto) }}">
                    @csrf @method('PUT')

                    <div class="space-y-3">
                        @foreach($catalogo as $slug => $modulo)
                        <label class="flex items-start gap-4 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-pindoor-accent hover:bg-gray-50 transition">
                            <input type="checkbox"
                                   name="modulos[]"
                                   value="{{ $slug }}"
                                   @checked(in_array($slug, $punto->modulos_habilitados ?? []))
                                   class="mt-0.5 rounded border-gray-300 text-pindoor-accent focus:ring-pindoor-accent">
                            <div>
                                <div class="font-semibold text-gray-800 text-sm">
                                    {{ $modulo['emoji'] }} {{ $modulo['label'] }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $modulo['desc'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="flex justify-between items-center mt-6">
                        <a href="{{ route('admin.clientes') }}"
                           class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                        <button type="submit"
                                class="px-6 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                            Guardar módulos
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
