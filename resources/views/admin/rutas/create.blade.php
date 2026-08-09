<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva ruta</h2>
            <a href="{{ route('admin.rutas.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-semibold">← Volver</a>
        </div>
    </x-slot>

    <div class="py-8 w-[90vw] mx-auto">
        @include('admin.rutas._form', [
            'action' => route('admin.rutas.store'),
            'method' => 'POST',
            'ruta'   => null,
        ])
    </div>
</x-admin-layout>
