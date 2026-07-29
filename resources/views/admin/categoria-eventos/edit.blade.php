<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categoria-eventos.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-extrabold text-xl text-gray-900">Editar «{{ $categoria->nombre }}»</h2>
                <p class="text-sm text-gray-400 mt-0.5">En uso en {{ $categoria->usos }} evento(s)/panorama(s)</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto px-4 sm:px-6" style="max-width:600px">

            @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.categoria-eventos.update', $categoria) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Datos básicos</h3>
                    @include('admin.categoria-eventos._form')
                </div>

                <div class="flex items-center gap-2 mt-5">
                    <button type="submit"
                            class="flex-1 bg-[#fc5648] text-white py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                        Guardar cambios
                    </button>
                    <a href="{{ route('admin.categoria-eventos.index') }}"
                       class="flex-1 text-center py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
