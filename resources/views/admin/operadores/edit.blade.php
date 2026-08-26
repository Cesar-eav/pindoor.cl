<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar operador — {{ $operador->nombre }}</h2>
            <a href="{{ route('admin.operadores.rutas.index', $operador) }}"
               class="bg-[#fc5648] hover:bg-[#e64536] text-white text-sm font-bold px-4 py-2 rounded-xl transition">
                🎟️ Gestionar ticketera
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.operadores.update', $operador) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('admin.operadores._form', ['operador' => $operador])
            </form>
        </div>
    </div>
</x-admin-layout>
