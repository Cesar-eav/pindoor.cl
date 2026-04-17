<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva categoría</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

                <form action="{{ route('admin.categorias.store') }}" method="POST" class="space-y-5">
                    @csrf
                    @include('admin.categorias._form')
                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-[#fc5648] text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                            Crear
                        </button>
                        <a href="{{ route('admin.categorias.index') }}"
                           class="px-6 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
