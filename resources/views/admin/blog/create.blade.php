<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva guía</h2>
            <a href="{{ route('admin.blog.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-semibold">← Volver</a>
        </div>
    </x-slot>

    <div class="py-8 w-[90vw] mx-auto">
        @include('admin.blog._form', [
            'action'  => route('admin.blog.store'),
            'method'  => 'POST',
            'post'    => null,
        ])
    </div>

    @include('admin.blog._editor-scripts', ['post' => null])
</x-admin-layout>
