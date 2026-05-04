<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi Negocio</h2>
    </x-slot>

    <div class="py-16">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10">
                <div class="text-5xl mb-4">🏪</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">¡Activa tu perfil en Pindoor!</h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-8">
                    Sube una foto, elige tu categoría y marca tu ubicación en el mapa.
                    Tu negocio queda publicado de inmediato.
                </p>
                <a href="{{ route('cliente.nuevo') }}"
                   class="inline-block bg-[#fc5648] text-white font-bold px-8 py-3 rounded-2xl shadow-lg hover:bg-[#e83a2c] transition">
                    Crear mi perfil →
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
