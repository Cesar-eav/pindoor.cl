<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar post</h2>
            <a href="{{ route('admin.blog.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-semibold">← Volver</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('admin.blog._form', [
            'action' => route('admin.blog.update', $post),
            'method' => 'PUT',
            'post'   => $post,
        ])
    </div>

    @include('admin.blog._editor-scripts', ['post' => $post])
</x-app-layout>
