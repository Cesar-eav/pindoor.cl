<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo re-vival</h2>
            <a href="{{ route('admin.revival.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-semibold">← Volver</a>
        </div>
    </x-slot>

    <div class="py-8 w-[90vw] mx-auto">
        @include('admin.revival._form', [
            'action'   => route('admin.revival.store'),
            'method'   => 'POST',
            'revival'  => null,
        ])
    </div>

    @include('admin.revival._editor-scripts')
</x-admin-layout>
