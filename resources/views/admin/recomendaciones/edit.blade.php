<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.recomendaciones.index') }}"
               class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar recomendación</h2>
            <a href="{{ route('admin.recomendaciones.preview', $recomendacion) }}" target="_blank"
               class="ml-auto text-sm font-bold text-gray-400 hover:text-gray-700 transition">Vista previa ↗</a>
        </div>
    </x-slot>

    <div class="py-8 space-y-6">

            @if($errors->any())
            <div class="w-[90vw] mx-auto bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="recomendacion-form" action="{{ route('admin.recomendaciones.update', $recomendacion) }}" method="POST"
                  enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')
                @include('admin.recomendaciones._form')
                <div class="w-[90vw] mx-auto flex justify-end gap-3">
                    <button type="submit" name="accion" value="seguir"
                            class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition">
                        Guardar y seguir editando
                    </button>
                    <button type="submit" name="accion" value="guardar"
                            class="bg-gray-900 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-black transition">
                        Guardar cambios
                    </button>
                </div>
            </form>

    </div>

    @include('admin.recomendaciones._editor-scripts')
</x-admin-layout>
