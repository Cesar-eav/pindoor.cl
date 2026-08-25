<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Activar cliente &mdash; {{ $punto->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Info del punto --}}
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 mb-6">
                <p class="text-xs text-gray-400 uppercase font-bold mb-2">Negocio a activar</p>
                <div class="text-gray-800 font-semibold text-lg">{{ $punto->title }}</div>
                <div class="text-sm text-gray-500 mt-1">
                    {{ $punto->categoria?->icono }} {{ $punto->categoria?->nombre ?? '—' }}
                    &middot; {{ $punto->sector }}
                </div>
            </div>

            {{-- Formulario credenciales --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3 mb-5">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                {{-- Vincular a un usuario cliente ya existente --}}
                @if($usuariosSinPunto->isNotEmpty())
                <form method="POST" action="{{ route('admin.clientes.activar', $punto) }}">
                    @csrf
                    <div class="space-y-3">
                        <x-input-label for="user_id_existente" value="Seleccionar usuario cliente" />
                        {{-- Lista visual de usuarios con sus logos --}}
                        <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            @foreach($usuariosSinPunto as $u)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:border-[#fc5648] hover:bg-red-50 transition has-[:checked]:border-[#fc5648] has-[:checked]:bg-red-50">
                                <input type="radio" name="user_id_existente" value="{{ $u->id }}" required class="accent-[#fc5648]">
                                @if($u->imagen_logo)
                                    <img src="{{ asset('storage/' . $u->imagen_logo) }}"
                                         alt="{{ $u->name }}"
                                         class="w-10 h-10 rounded-lg object-cover border border-gray-200 shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-xl shrink-0">👤</div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">{{ $u->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $u->email }}</p>
                                </div>
                                @if($u->imagen_logo)
                                    <span class="ml-auto text-[10px] bg-green-100 text-green-700 font-bold px-2 py-0.5 rounded-full shrink-0">Logo ✓</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-6">
                        <a href="{{ route('admin.clientes') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Volver</a>
                        <button type="submit" class="px-6 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                            Vincular y activar
                        </button>
                    </div>
                </form>
                @else
                <p class="text-sm text-gray-400 italic">
                    No hay usuarios cliente existentes para vincular a este punto.
                    @if($punto->esBasico())
                        El dueño debe reclamar el perfil desde
                        <a href="{{ route('puntos.activar', $punto->slug) }}" class="text-pindoor-accent hover:underline" target="_blank">la ficha pública</a>
                        para crear su propia cuenta.
                    @endif
                </p>
                @endif
            </div>

        </div>
    </div>
</x-admin-layout>
