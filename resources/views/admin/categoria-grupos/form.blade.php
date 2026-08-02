<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categoria-grupos.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Grupos</a>
            <h2 class="font-extrabold text-xl text-gray-900">{{ $grupo ? 'Editar grupo' : 'Nuevo grupo' }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto px-4 sm:px-6 max-w-3xl">

            @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form method="POST"
                  action="{{ $grupo ? route('admin.categoria-grupos.update', $grupo) : route('admin.categoria-grupos.store') }}">
                @csrf
                @if($grupo) @method('PUT') @endif

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <x-input-label for="nombre" value="Nombre del grupo *" />
                            <x-text-input id="nombre" name="nombre" class="block mt-1 w-full"
                                          value="{{ old('nombre', $grupo->nombre ?? '') }}" required autofocus />
                        </div>
                        <div>
                            <x-input-label for="orden" value="Orden" />
                            <x-text-input id="orden" name="orden" type="number" min="0" class="block mt-1 w-full"
                                          value="{{ old('orden', $grupo->orden ?? 0) }}" />
                        </div>
                        <div class="sm:col-span-3">
                            <x-input-label for="icono" value="Ícono (nombre de Font Awesome, ej: utensils, landmark, mountain-sun)" />
                            <div class="flex items-center gap-3 mt-1">
                                <span id="icono-preview" class="w-11 h-11 shrink-0 rounded-xl bg-[#fff0ef] text-[#fc5648] flex items-center justify-center">
                                    <i class="fa-solid fa-{{ old('icono', $grupo->icono ?? 'tag') }}"></i>
                                </span>
                                <x-text-input id="icono" name="icono" class="block w-full"
                                              value="{{ old('icono', $grupo->icono ?? '') }}"
                                              placeholder="tag"
                                              oninput="document.querySelector('#icono-preview i').className = 'fa-solid fa-' + (this.value || 'tag')" />
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Busca nombres válidos en <a href="https://fontawesome.com/search?o=r&s=solid" target="_blank" class="text-[#fc5648] hover:underline">fontawesome.com</a> (estilo "solid").</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                    <h3 class="font-bold text-gray-800 mb-1">Categorías dentro de este grupo</h3>
                    <p class="text-xs text-gray-400 mb-4">Cada categoría solo puede pertenecer a un grupo. Si la marcas aquí y ya estaba en otro grupo, se mueve a este.</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($categorias as $cat)
                        <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 border border-gray-100 rounded-xl px-3 py-2 transition">
                            <input type="checkbox" name="categorias[]" value="{{ $cat->id }}"
                                   @checked(in_array($cat->id, old('categorias', $seleccionadas)))
                                   class="rounded border-gray-300 text-[#fc5648] focus:ring-[#fc5648]">
                            <span class="text-sm text-gray-700">{{ $cat->nombre }}</span>
                            @if($cat->grupo_id && (!$grupo || $cat->grupo_id !== $grupo->id))
                                <span class="text-[10px] text-gray-400 ml-auto shrink-0">({{ $cat->grupo->nombre ?? '' }})</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.categoria-grupos.index') }}" class="px-5 py-2.5 text-sm text-gray-500 hover:text-gray-800 transition">Cancelar</a>
                    <button type="submit" class="bg-[#fc5648] text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                        {{ $grupo ? 'Guardar cambios' : 'Crear grupo' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-admin-layout>
