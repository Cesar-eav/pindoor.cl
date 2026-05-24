<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Catálogo de productos</h2>
            <a href="{{ route('cliente.perfil') }}" class="text-sm text-gray-500 hover:text-gray-700 font-semibold">← Mi negocio</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
        @endif

        {{-- Agregar producto --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4">Agregar producto</h3>
            <form action="{{ route('cliente.productos.store') }}" method="POST" enctype="multipart/form-data"
                  class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nombre *</label>
                    <input type="text" name="nombre" required placeholder="Ej: Polera estampada"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Precio</label>
                    <input type="text" name="precio" placeholder="Ej: $12.000 / Desde $5.000"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Imagen</label>
                    <input type="file" name="imagen" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-[#fff0ef] file:text-[#fc5648] hover:file:bg-[#ffe0dd] cursor-pointer">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Descripción corta</label>
                    <input type="text" name="descripcion" placeholder="Descripción opcional del producto"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit"
                            class="bg-[#fc5648] text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                        + Agregar producto
                    </button>
                </div>
            </form>
        </div>

        {{-- Lista de productos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">Mis productos <span class="text-gray-400 font-normal">({{ $productos->count() }})</span></h3>
            </div>

            @if($productos->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400">
                <div class="text-4xl mb-3">🛍️</div>
                <p class="font-semibold">Aún no tienes productos cargados.</p>
                <p class="text-sm mt-1">Agrégalos arriba y aparecerán en tu catálogo público.</p>
            </div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($productos as $producto)
                <div class="flex items-center gap-4 px-6 py-4">
                    {{-- Imagen --}}
                    <div class="shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-gray-100 border border-gray-100">
                        @if($producto->imagen)
                            <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-2xl text-gray-300">📦</div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ $producto->nombre }}</p>
                        @if($producto->precio)
                        <p class="text-sm text-[#fc5648] font-bold">{{ $producto->precio }}</p>
                        @endif
                        @if($producto->descripcion)
                        <p class="text-xs text-gray-400 truncate">{{ $producto->descripcion }}</p>
                        @endif
                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center gap-3 shrink-0">
                        <button onclick="toggleEditar({{ $producto->id }})"
                                class="text-xs font-bold text-blue-600 hover:underline">Editar</button>
                        <form action="{{ route('cliente.productos.destroy', $producto) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar «{{ addslashes($producto->nombre) }}»?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-red-400 hover:underline">Eliminar</button>
                        </form>
                    </div>
                </div>

                {{-- Fila edición inline --}}
                <div id="editar-{{ $producto->id }}" class="hidden bg-gray-50 px-6 py-4 border-t border-dashed border-gray-200">
                    <form action="{{ route('cliente.productos.update', $producto) }}" method="POST"
                          enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Nombre</label>
                            <input type="text" name="nombre" value="{{ $producto->nombre }}" required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Precio</label>
                            <input type="text" name="precio" value="{{ $producto->precio }}"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 mb-1">Descripción</label>
                            <input type="text" name="descripcion" value="{{ $producto->descripcion }}"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Nueva imagen</label>
                            <input type="file" name="imagen" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#fff0ef] file:text-[#fc5648]">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                    class="bg-gray-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-black transition">
                                Guardar
                            </button>
                            <button type="button" onclick="toggleEditar({{ $producto->id }})"
                                    class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-500 hover:bg-gray-100 transition">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

<script>
function toggleEditar(id) {
    document.getElementById('editar-' + id).classList.toggle('hidden');
}
</script>
</x-app-layout>
