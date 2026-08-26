<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo operador turístico</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.operadores.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.operadores._form', ['operador' => null])
            </form>
        </div>
    </div>
</x-admin-layout>
