<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-gray-900">Configuración general</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto px-4 sm:px-6" style="max-width:600px">

            @if(session('success'))
                <div class="mb-5 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.configuracion.actualizar') }}" method="POST">
                @csrf
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="aprobacion_negocios_activa" value="1"
                               @checked($aprobacionActiva)
                               class="mt-1 rounded border-gray-300 text-[#fc5648] focus:ring-[#fc5648]">
                        <div>
                            <p class="font-bold text-gray-900">Requerir aprobación de admin para negocios nuevos</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Con esto activo, el onboarding le pide al negocio un WhatsApp de contacto y su ficha
                                queda oculta hasta que un admin la revise y apruebe desde
                                <a href="{{ route('admin.clientes') }}" class="text-[#fc5648] underline">Clientes</a>.
                                Los negocios que ya existen no se ven afectados.
                            </p>
                        </div>
                    </label>
                </div>

                <button type="submit"
                        class="mt-5 bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                    Guardar
                </button>
            </form>

        </div>
    </div>
</x-app-layout>
