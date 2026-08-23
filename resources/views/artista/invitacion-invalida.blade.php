<x-guest-layout>
    <div class="text-center py-6">
        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-50 flex items-center justify-center text-2xl">⚠️</div>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Invitación no válida</h2>
        @if($emailIncorrecto ?? false)
            <p class="text-sm text-gray-500">
                Esta invitación es para otro correo. Cierra sesión e inicia con el correo al que fue enviada.
            </p>
        @else
            <p class="text-sm text-gray-500">
                Este link ya fue usado, venció o no existe. Pide a quien te invitó que envíe una nueva invitación.
            </p>
        @endif
        <a href="{{ url('/') }}" class="inline-block mt-6 text-sm font-bold text-[#fc5648] hover:underline">Volver a Pindoor</a>
    </div>
</x-guest-layout>
