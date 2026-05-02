<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.panoramas.index') }}"
               class="text-gray-400 hover:text-gray-600 transition text-sm font-bold">← Volver</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar panorama</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

                @if($errors->any())
                    <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.panoramas.update', $panorama) }}" method="POST"
                      enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @include('admin.panoramas._form')

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="bg-[#fc5648] text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                            Guardar cambios
                        </button>
                        <a href="{{ route('admin.panoramas.index') }}"
                           class="px-6 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
