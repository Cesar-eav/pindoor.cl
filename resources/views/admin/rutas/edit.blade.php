<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar ruta</h2>
            <a href="{{ route('admin.rutas.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-semibold">← Volver</a>
        </div>
    </x-slot>

    <div class="py-8 w-[90vw] mx-auto">
        @include('admin.rutas._form', [
            'action' => route('admin.rutas.update', $ruta),
            'method' => 'PUT',
            'ruta'   => $ruta,
        ])
    </div>
</x-admin-layout>
